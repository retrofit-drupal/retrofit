# Supported hooks

## `hook_menu`

Converts the legacy menu system hook to define routes and menu link plugins.

Normal routes use the `\Retrofit\Drupal\Controller\PageCallbackController` controller. If a route was for a form using 
the `drupal_get_form` callback, `\Retrofit\Drupal\Controller\DrupalGetFormController` is used instead. See [Form API](supported-apis.md#form-api) 
for information on Form API backward compatibility.

TODO `\Retrofit\Drupal\Routing\HookMenuRoutes::alterRoutes`
TODO `\Retrofit\Drupal\Plugin\Derivative\MenuLinkDeriver`

Not yet supported
- Actions
- Tasks

## Field hooks

### `hook_field_info`

`\Retrofit\Drupal\Plugin\Derivative\FieldItemDeriver::getDerivativeDefinitions`

### `hook_field_schema`

`\Retrofit\Drupal\Plugin\Field\FieldType\FieldItem::schema`

### `hook_field_is_empty`

`\Retrofit\Drupal\Plugin\Field\FieldType\FieldItem::isEmpty`

### `hook_field_validate`

`\Retrofit\Drupal\Plugin\Field\FieldType\FieldItemList::validate`

## `hook_permission`

`\Retrofit\Drupal\User\HookPermissions` decorated the permission provider and invokes `hook_permission`.

## Block hooks

### `hook_block_info`

`\Retrofit\Drupal\Plugin\Derivative\BlockDeriver::getDerivativeDefinitions`

### `hook_block_view`

`\Retrofit\Drupal\Plugin\Block\Block::build`

### `hook_block_configure`

`\Retrofit\Drupal\Plugin\Block\Block::blockForm`

### `hook_block_save`

`\Retrofit\Drupal\Plugin\Block\Block::blockSubmit`
