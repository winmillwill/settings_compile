# Drupal Settings Compile

We are using json for our config tuples for drupal, which is great, but we want
to be a bit faster if possible. Lets compile it to php arrays.

## Prior Art

Symfony2 takes config in json, yaml, and php array format. Perhaps it has tools
for 'transcoding' between them?

## Other inputs

Since we wish to check an environment variable, we now need to know which one to
check, which gets tricky for several sites on the same server.
