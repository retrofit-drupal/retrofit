# Caveats with Retrofit for Drupal

## Escaped output

Drupal 7 had mechanisms in place to manually filter user input. These were removed when Drupal adopted Twig, as Twig has
auto-escaping built in. With Retrofit for Drupal's support for PHPTemplate files, values contained user input may not
be filtered.

See https://github.com/retrofit-drupal/retrofit/issues/15

## Namespaced functions

See [namespaced functions](namespaced-functions.md).

## Fixing placeholders in `t()`

The `!` placeholder is no longer valid. Placeholders using `!` in `t()` need to be manually changed to `@` or `:`.
