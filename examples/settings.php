<?php

require __DIR__.'/../vendor/autoload.php';

if (!file_exists('settings.comp.php')) {
  $compiler = new Drupal\Settings\Compiler(
    __DIR__.'/conf.d'
  );
  $compiler->write(__DIR__.'/settings.comp.php');
}

require __DIR__.'/settings.comp.php';
