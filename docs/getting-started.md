# Getting started with Retrofit for Drupal

Retrofit for Drupal allows you to run your custom Drupal 7 modules and themes in a Drupal 10 site, without rewriting your code.

Get started with a few simple steps:

1. Create a Drupal code base.
2. Add retrofit-drupal/retrofit
3. Copy your Drupal 7 modules and themes
4. Update your Drupal 7 module and themes `.info` files to `info.yml` files
5. Migrate your data!

```sh
composer create-project \ 
 drupal/recommended-project \
 drupal
 
cd drupal

composer require retrofit-drupal/retrofit
```

## Updating your `.info` files to `info.yml` files

### Modules

You must modify your Drupal 7 modules `.info` file to `info.yml`.

* Add `type: module`
* Add `core_version_requirement: >= 10`

### Themes

You must modify your Drupal 7 theme's `.info` file into a `info.yml` format.

* Add `type: theme`
* Add `core_version_requirement: >= 10`

You must also rename `template.php` to `THEME_NAME.theme`, the next extension file format.

You must create an asset library for your previously declared `stylesheets` and add a `libraries` definition
to attach those to the page. In the future, this may be automated with [#26](https://github.com/retrofit-drupal/retrofit/issues/26).
