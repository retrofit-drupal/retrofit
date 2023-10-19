# Caveats with Retrofit for Drupal

## Escaped output

Drupal 7 had mechanisms in place to manually filter user input. These were removed when Drupal adopted Twig, as Twig has
auto-escaping built in. With Retrofit for Drupal's support for PHPTemplate files, values contained user input may not
be filtered.

See https://github.com/retrofit-drupal/retrofit/issues/15

## Namespaced functions

### `module_load_include`

`module_load_include` is available as `Retrofit\Drupal\module_load_include`.

## Fixing placeholders in `t()`

The `!` placeholder is no longer valid. Placeholders using `!` in `t()` need to be manually changed to `@` or `:`.
