# Supported APIs

This documentation explains what [Drupal 7 APIs](https://www.drupal.org/docs/7/api) have compatibility layers provided 
by Retrofit for Drupal.

## Menu system

In Drupal 7, the menu system provided routing and menu links. These are now two distinct APIs in Drupal, 
the [Routing system](https://www.drupal.org/docs/drupal-apis/routing-system) and [Menu API](https://www.drupal.org/docs/drupal-apis/menu-api).

Retrofit for Drupal provides a backward compatibility layer that converts `hook_menu` into routes and menu link plugins.

See [`hook_menu`](supported-hooks.md#hook_menu) for more details.

## Theme API 

* Adds support for theme functions and PHPTemplates.
* Process hooks are run as preprocess hooks.

## Block API

TODO

- `\Retrofit\Drupal\Plugin\Derivative\BlockDeriver`
- `\Retrofit\Drupal\Plugin\Block\Block`

See [supported hooks](supported-hooks.md#block-hooks). 

## Field API

TODO

- `\Retrofit\Drupal\Field\FieldTypePluginManager`
- `\Retrofit\Drupal\Plugin\Derivative\FieldItemDeriver`
- `\Retrofit\Drupal\Plugin\Derivative\FieldFormatterDeriver`
- `\Retrofit\Drupal\Plugin\Field\FieldType\DecoratedFieldItem`
- `\Retrofit\Drupal\Plugin\Field\FieldType\FieldItem`
- `\Retrofit\Drupal\Plugin\Field\FieldType\FieldItemList`

See [supported hooks](supported-hooks.md#field-hooks).

## Form API

TODO 
* Document decorated form state to allow array access

* `\Retrofit\Drupal\Controller\DrupalGetFormController`
* `\Retrofit\Drupal\Form\DrupalGetForm`
* `\Retrofit\Drupal\Form\ArrayAccessFormState`
* `\Retrofit\Drupal\Form\FormValidator`
