# Supported hooks

## `hook_menu`

Converts the legacy menu system hook to define routes and menu link plugins.

Normal routes use the `\Retrofit\Drupal\Controller\PageCallbackController` controller. If a route was for a form using 
the `drupal_get_form` callback, `\Retrofit\Drupal\Controller\DrupalGetFormController` is used instead. See [Form API](supported-apis.md#form-api) 
for information on Form API backward compatibility.

TODO `\Retrofit\Drupal\Plugin\Derivative\MenuLinkDeriver`

Not yet supported
- Actions
- Tasks
