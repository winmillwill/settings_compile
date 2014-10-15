# Drupal Settings Compile

## Motivation

You should be able to treat the Drupal configuration represented in settings.php
like a simple dictionary that can be serialized in any format that can represent
a dictionary, because that's what settings.php actually is. This makes it simple
to change the settings without knowing php and even without logging onto the
server or making any git commits. You would want to allow this because your
application will likely need to exist in several different environments at any
arbitrary commit in its history.

An alternate strategy would be to version a settings.php that sets the
configuration variables according to Environment Variables fetched with
`getenv()`. If you have control over the visibility of Environment Variables to
the running php process, then that approach is preferable.

## How To

Install this project with composer.

See `examples/config.yml` and witness the resulting `settings.php` when invoking
like this:

```
vendor/bin/settings_compile vendor/winmillwill/settings_compile/examples/config.yml settings.php
```

or you could even do this:

```
vendor/bin/settings_compile https://raw.githubusercontent.com/winmillwill/settings_compile/master/examples/config.yml settings.php
```

The command simply takes a path to a correctly formatted yaml file and the
desired path at which to write the resulting settings.php file.

## Schema

You can see this stated fairly plainly in
[Drupal\Settings\Schema](github.com/winmillwill/settings_compile/blob/master/src/Schema.php)
but the gist is that you address the globals that are available for modification
in settings.php by using the *settings* key, hence your databases hash would
start like this:

```yaml
drupal:
...
  settings:
    databases:
      ...
```

and the keys you can set under *settings* is limited to this list:

* `databases`
* `cookie_domain`
* `conf`
* `installed_profile`
* `update_free_access`
* `db_url`
* `db_prefix`
* `drupal_hash_salt`
* `is_https`
* `base_secure_url`
* `base_insecure_url`

You can require your composer autoloader (or any other php file) like this (for 2.0.x):

```
drupal:
...
  include:
    require:
      - $DRUPAL_ROOT/relative/path/to/vendor/autoload.php
```

This works because all values are naively quoted in 2.0.x.

You can specify the full path without aid of the `DRUPAL_ROOT` macro, though
that would require whoever edits the yaml file to know the full path to the
Drupal application on whatever server.

You can additionally effect ini settings like this:

```
drupal:
...
  ini:
    xdebug.show_exception_trace: 1
```

Starting with 2.1.1, you can disable quoting for your value by prefixing with
a `%`, which gives you full access to php:

```
drupal:
...
  include:
    require:
      - %DRUPAL_ROOT . '/relative/path/to/vendor/autoload.php'
```

Similarly, any value starting with a `$` is also naively left untreated.

## Alternate Strategy with Environment Variables

You can also use the `%` escaping to make a yaml file that can very easily model
your config as something that is aware of its environment:

```
drupal:
...
  settings:
    db_url: %getenv('DRUPAL_DB_URL')
```

This makes setting up for your PAAS simple, though it does begin to beg the
question as to whether you gain anything by using yaml instead of just
versioning the php file and letting another system manage setting the
environment variables, as described above in [Motivation](#Motivation).

## Roadmap

* Support use of php constants in the yaml file
* Support use of Environment Variables in the yaml file
