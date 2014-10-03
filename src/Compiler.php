<?php

namespace Drupal\Settings;

use Symfony\Component\Yaml\Parser;
use Symfony\Component\Config\Definition\Processor;
use Drupal\Settings\Schema;

class Compiler
{
    public $globals = array(
        'databases',
        'cookie_domain',
        'conf',
        'installed_profile',
        'update_free_access',
        'db_url',
        'db_prefix',
        'drupal_hash_salt',
        'is_https',
        'base_secure_url',
        'base_insecure_url'
    );

    function __construct($configFile)
    {
        $yaml = new Parser();
        $config = $yaml->parse(file_get_contents($configFile));
        $processor = new Processor();
        $this->config = $processor->processConfiguration(new Schema(), $config);
    }

    function settingsPreprocess()
    {
        if (isset($this->config['settings']['db_url'])) {
            $db = &$this->config['settings']['databases']['default']['default'];
            $dbURL = parse_url($this->config['settings']['db_url']);
            $db['driver']   = $dbURL['scheme'];
            $db['username'] = $dbURL['user'];
            $db['password'] = $dbURL['pass'];
            $db['database'] = trim($dbURL['path'], '/');
            $db['host']     = $dbURL['host'];
        }
        $salt = hash('sha256', implode('.', array(getcwd(), microtime())));
        $this->config['settings']['drupal_hash_salt'] = $salt;
    }

    function write($path)
    {
        $this->settingsPreprocess();
        $settings = "<?php\n";
        foreach ($this->config['settings'] as $settingName => $settingValue) {
          $setting = "\$$settingName=";
          $setting .= is_array($settingValue)
            ? var_export($settingValue, TRUE)
            : "\"$settingValue\"";
          $settings .= "$setting;";
        }
        foreach ($this->config['ini'] as $iniDirective => $iniValue) {
            $settings .= "ini_set('$iniDirective', '$iniValue');";
        }
        foreach ($this->config['include'] as $type => $includes) {
            foreach ($includes as $includePath) {
                $settings .= "$type \"$includePath\";";
            }
        }
        file_put_contents($path, $settings);
    }
}
