<?php

namespace Drupal\Settings;

class Compiler
{
    public $globals = [
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
    ];

    public $confPath;

    function __construct($confPath)
    {
        $this->confPath     = $confPath;
        $this->globalsConfD = $confPath . '/globals.conf.d';
        $this->iniConfD     = $confPath . '/ini.conf.d';
        $this->load();
    }

    function load()
    {
        $this->loadIniDirectives();
        $this->loadGlobalJson();
    }

    function loadGlobalJson()
    {
        $flags = \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::FOLLOW_SYMLINKS;
        $iterator = new \RecursiveDirectoryIterator($this->globalsConfD, $flags);
        $it       = new \RecursiveIteratorIterator(
            $iterator,
            \RecursiveIteratorIterator::SELF_FIRST
        );
        $depth_matrix = array();
        foreach ($it as $file) {
            if (!$it->isDir()) {
                $path = $it->getSubPathname();
                $depth = count(explode('/', $path));
                $info = json_decode(
                    file_get_contents($this->globalsConfD . '/' . $path),
                    TRUE
                );
                $depth_matrix[$depth][] = $this->arrayPathDepthSet($path, $info);
            }
        }
        foreach ($depth_matrix as $depth => $configs) {
            $depth_matrix[$depth] = call_user_func_array(
                'array_merge_recursive',
                $configs
            );
        }

        // Ensure that the deepest configs override others.
        krsort($depth_matrix);
        $globals = call_user_func_array(
            'array_merge_recursive',
            $depth_matrix
        );
        $default = $globals['globals'];
        unset($globals['globals']);
        $this->config['global'] = array_merge_recursive($default, $globals);
    }

    function importGlobals()
    {
        foreach ($this->globals as $global) {
            global $$global;
        }
        $config = $this->config['global'];
        extract($config);
    }

    function write($path)
    {
        $global       = $this->config['global'];
        $globalsArray = var_export($global, TRUE);
        $settings = "<?php\n";
        $settings .= "extract($globalsArray);";
        foreach($this->config['ini'] as $directive => $value) {
            $settings .= "ini_set($directive, $value);";
        }
        file_put_contents($path, $settings);
    }

    function loadIniDirectives()
    {
        $files = glob($this->iniConfD . '/*.ini');
        $this->config['ini'] = array();
        foreach ($files as $file) {
            $this->config['ini'] += parse_ini_file($file);
        }
    }

    function importIniDirectives()
    {
        foreach ($this->config['ini'] as $directive => $value) {
            ini_set($directive, $value);
        }
    }

    function arrayPathDepthSet($path, $info)
    {
        $path_parts = explode('/', $path);
        $filename = array_pop($path_parts);
        $base = current(explode('.', $filename, -1));
        $info = array($base => $info);
        while($key = array_pop($path_parts)) {
            $info = array($key => $info);
        }
        return $info;
    }
}
