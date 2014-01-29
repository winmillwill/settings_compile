# Drupal Settings Compile

## Motivation

Loading config from an arbitrary directory on a server instead of templating
settings.php or versioning your database config like there's only one way to
configure your server is pretty rad.

What sucks is still having to put in a line to tell settings.php where the
settings actually are. Plus, loading json on every page request is for chumps.
Why not compile that config directory into php on a performance and deployment
tip?

## How To

Peep the examples/settings.php

You can straight up use this simple Compiler class in your php code like
a bawss.

You can also just install this project with composer and be all like

```vendor/bin/settings_compile path/to/confd path/to/site/settings.comp.php```

You just need to make sure that your actual `settings.php` file loads your
compiled settings, or actually just overwrite it if that's your thing.

An easy compromise is to let settings.php look for the compiled settings and
trigger the compiler if it doesn't find it, but to just compile as part of
deployment.
