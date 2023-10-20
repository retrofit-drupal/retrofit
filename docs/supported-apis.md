# Supported APIs

This documentation explains what [Drupal 7 APIs](https://www.drupal.org/docs/7/api) have compatibility layers provided 
by Retrofit for Drupal.

## Menu system

In Drupal 7, the menu system provided routing and menu links. These are now two distinct APIs in Drupal, 
the [Routing system](https://www.drupal.org/docs/drupal-apis/routing-system) and [Menu API](https://www.drupal.org/docs/drupal-apis/menu-api).

Retrofit for Drupal provides a backward compatibility layer that converts `hook_menu` into routes and menu link plugins.

See [`hook_menu`](supported-hooks.md#hookmenu) for more details.

## Permissions API (`hook_permissions`)

TODO 

## Theme API (`hook_theme`)

TODO

## Block API (`hook_block_*`)

## Field API

TODO

## Form API

TODO 

* `\Retrofit\Drupal\Controller\DrupalGetFormController`
* `\Retrofit\Drupal\Form\DrupalGetForm`
* `\Retrofit\Drupal\Form\ArrayAccessFormState`
