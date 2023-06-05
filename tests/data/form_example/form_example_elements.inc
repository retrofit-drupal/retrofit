<?php

/**
 * @file
 * This is an example demonstrating how a module can define custom form and
 * render elements.
 *
 * Form elements are already familiar to anyone who uses Form API. They share
 * history with render elements, which are explained in the
 * @link render_example.module Render Example @endlink. Examples
 * of core form elements are 'textfield', 'checkbox' and 'fieldset'. Drupal
 * utilizes hook_elements() to define these FAPI types, and this occurs in
 * the core function system_elements().
 *
 * Each form element has a #type value that determines how it is treated by
 * the Form API and how it is ultimately rendered into HTML.
 * hook_element_info() allows modules to define new element types, and tells
 * the Form API what default values they should automatically be populated with.
 *
 * By implementing hook_element_info() in your own module, you can create custom
 * form (or render) elements with their own properties, validation and theming.
 *
 * In this example, we define a series of elements that range from trivial
 * (a renamed textfield) to more advanced (a telephone number field with each
 * portion separately validated).
 *
 * Since each element can use arbitrary properties (like #process or #dropthis)
 * it can be quite complicated to figure out what all the properties actually
 * mean. This example won't undertake the exhaustive task of explaining them
 * all, as that would probably be impossible.
 */

/**
 * @todo: Some additional magic things to explain:
 * - #process and process callback (and naming) (in forms)
 * - #value and value callback (and naming of the above)
 * - #theme and #theme_wrappers
 * - What is #return_value?
 * - system module provides the standard default elements.
 * - What are all the things that can be defined in hook_element_info() and
 *   where do the defaults come from?
 * - Form elements that have a type that has a matching type in the element
 *   array created by hook_element_info() get those items merged with them.
 * - #value_callback is called first by form_builder(). Its job is to figure
 *   out what the actual value of the element, using #default_value or whatever.
 * - #process is then called to allow changes to the whole element (like adding
 *   child form elements.)
 * - #return_value: chx: you need three different values for form API. You need
 *   the default value (#default_value), the value for the element if it gets
 *   checked )#return_value) and then #value which is either 0 or the
 *   #return_value
 */

/**
 * Utility function providing data for form_example_element_info().
 *
 * This defines several new form element types.
 *
 * - form_example_textfield: This is actually just a textfield, but provides
 *   the new type. If more were to be done with it a theme function could be
 *   provided.
 * - form_example_checkbox: Nothing more than a regular checkbox, but uses
 *   an alternate theme function provided by this module.
 * - form_example_phonenumber_discrete: Provides a North-American style
 *   three-part phonenumber where the value of the phonenumber is managed
 *   as an array of three parts.
 * - form_example_phonenumber_combined: Provides a North-American style
 *   three-part phonenumber where the actual value is managed as a 10-digit
 *   string and only broken up into three parts for the user interface.
 *
 * form_builder() has significant discussion of #process and #value_callback.
 * See also hook_element_info().
 *
 * system_element_info() contains the Drupal default element types, which can
 * also be used as examples.
 */
function _form_example_element_info() {
  // form_example_textfield is a trivial element based on textfield that
  // requires only a definition and a theme function. In this case we provide
  // the theme function using the parent "textfield" theme function, but it
  // would by default be provided in hook_theme(), by a "form_example_textfield"
  // theme implementation, provided by default by the function
  // theme_form_example_textfield().  Note that the 'form_example_textfield'
  // element type is completely defined here. There is no further code required
  // for it.
  $types['form_example_textfield'] = array(
    // #input = TRUE means that the incoming value will be used to figure out
    // what #value will be.
    '#input' => TRUE,

    // Use theme('textfield') to format this element on output.
    '#theme' => array('textfield'),

    // Do not provide autocomplete.
    '#autocomplete_path' => FALSE,

    // Allow theme('form_element') to control the markup surrounding this
    // value on output.
    '#theme_wrappers' => array('form_element'),
  );

  // form_example_checkbox is mostly a copy of the system-defined checkbox
  // element.
  $types['form_example_checkbox'] = array(
    // This is an HTML <input>.
    '#input' => TRUE,

    // @todo: Explain #return_value.
    '#return_value' => TRUE,

    // Our #process array will use the standard process functions used for a
    // regular checkbox.
    '#process' => array('form_process_checkbox', 'ajax_process_form'),

    // Use theme('form_example_checkbox') to render this element on output.
    '#theme' => 'form_example_checkbox',

    // Use theme('form_element') to provide HTML wrappers for this element.
    '#theme_wrappers' => array('form_element'),

    // Place the title after the element (to the right of the checkbox).
    // This attribute affects the behavior of theme_form_element().
    '#title_display' => 'after',

    // We use the default function name for the value callback, so it does not
    // have to be listed explicitly. The pattern for the default function name
    // is form_type_TYPENAME_value().
    // '#value_callback' => 'form_type_form_example_checkbox_value',
  );

  // This discrete phonenumber element keeps its values as the separate elements
  // area code, prefix, extension.
  $types['form_example_phonenumber_discrete'] = array(
    // #input == TRUE means that the form value here will be used to determine
    // what #value will be.
    '#input' => TRUE,

    // #process is an array of callback functions executed when this element is
    // processed. Here it provides the child form elements which define
    // areacode, prefix, and extension.
    '#process' => array('form_example_phonenumber_discrete_process'),

    // Validation handlers for this element. These are in addition to any
    // validation handlers that might.
    '#element_validate' => array('form_example_phonenumber_discrete_validate'),
    '#autocomplete_path' => FALSE,
    '#theme_wrappers' => array('form_example_inline_form_element'),
  );

  // Define form_example_phonenumber_combined, which combines the phone
  // number into a single validated text string.
  $types['form_example_phonenumber_combined'] = array(
    '#input' => TRUE ,
    '#process' => array('form_example_phonenumber_combined_process'),
    '#element_validate' => array('form_example_phonenumber_combined_validate'),
    '#autocomplete_path' => FALSE,
    '#value_callback'   => 'form_example_phonenumber_combined_value',
    '#default_value' => array(
      'areacode' => '',
      'prefix' => '',
      'extension' => '',
    ),
    '#theme_wrappers' => array('form_example_inline_form_element'),
  );
  return $types;
}


/**
 * Value callback for form_example_phonenumber_combined.
 *
 * Builds the current combined value of the phone number only when the form
 * builder is not processing the input.
 *
 * @param array $element
 *   Form element.
 * @param array $input
 *   Input.
 * @param array $form_state
 *   Form state.
 *
 * @return array
 *   The modified element.
 */
function form_example_phonenumber_combined_value(&$element, $input = FALSE, $form_state = NULL) {
  if (!$form_state['process_input']) {
    $matches = array();
    $match = preg_match('/^(\d{3})(\d{3})(\d{4})$/', $element['#default_value'], $matches);
    if ($match) {
      // Get rid of the "all match" element.
      array_shift($matches);
      list($element['areacode'], $element['prefix'], $element['extension']) = $matches;
    }
  }
  return $element;
}

/**
 * Value callback for form_example_checkbox element type.
 *
 * Copied from form_type_checkbox_value().
 *
 * @param array $element
 *   The form element whose value is being populated.
 * @param mixed $input
 *   The incoming input to populate the form element. If this is FALSE, meaning
 *   there is no input, the element's default value should be returned.
 *
 * @return int
 *   The value represented by the form element.
 */
function form_type_form_example_checkbox_value($element, $input = FALSE) {
  if ($input === FALSE) {
    return isset($element['#default_value']) ? $element['#default_value'] : 0;
  }
  else {
    return isset($input) ? $element['#return_value'] : 0;
  }
}

/**
 * Process callback for the discrete version of phonenumber.
 */
function form_example_phonenumber_discrete_process($element, &$form_state, $complete_form) {
  // #tree = TRUE means that the values in $form_state['values'] will be stored
  // hierarchically. In this case, the parts of the element will appear in
  // $form_state['values'] as
  // $form_state['values']['<element_name>']['areacode'],
  // $form_state['values']['<element_name>']['prefix'],
  // etc. This technique is preferred when an element has member form
  // elements.
  $element['#tree'] = TRUE;

  // Normal FAPI field definitions, except that #value is defined.
  $element['areacode'] = array(
    '#type' => 'textfield',
    '#size' => 3,
    '#maxlength' => 3,
    '#value' => $element['#value']['areacode'],
    '#required' => TRUE,
    '#prefix' => '(',
    '#suffix' => ')',
  );
  $element['prefix'] = array(
    '#type' => 'textfield',
    '#size' => 3,
    '#maxlength' => 3,
    '#required' => TRUE,
    '#value' => $element['#value']['prefix'],
  );
  $element['extension'] = array(
    '#type' => 'textfield',
    '#size' => 4,
    '#maxlength' => 4,
    '#value' => $element['#value']['extension'],
  );

  return $element;
}

/**
 * Validation handler for the discrete version of the phone number.
 *
 * Uses regular expressions to check that:
 *  - the area code is a three digit number.
 *  - the prefix is numeric 3-digit number.
 *  - the extension is a numeric 4-digit number.
 *
 * Any problems are shown on the form element using form_error().
 */
function form_example_phonenumber_discrete_validate($element, &$form_state) {
  if (isset($element['#value']['areacode'])) {
    if (0 == preg_match('/^\d{3}$/', $element['#value']['areacode'])) {
      form_error($element['areacode'], t('The area code is invalid.'));
    }
  }
  if (isset($element['#value']['prefix'])) {
    if (0 == preg_match('/^\d{3}$/', $element['#value']['prefix'])) {
      form_error($element['prefix'], t('The prefix is invalid.'));
    }
  }
  if (isset($element['#value']['extension'])) {
    if (0 == preg_match('/^\d{4}$/', $element['#value']['extension'])) {
      form_error($element['extension'], t('The extension is invalid.'));
    }
  }
  return $element;
}

/**
 * Process callback for the combined version of the phonenumber element.
 */
function form_example_phonenumber_combined_process($element, &$form_state, $complete_form) {
  // #tree = TRUE means that the values in $form_state['values'] will be stored
  // hierarchically. In this case, the parts of the element will appear in
  // $form_state['values'] as
  // $form_state['values']['<element_name>']['areacode'],
  // $form_state['values']['<element_name>']['prefix'],
  // etc. This technique is preferred when an element has member form
  // elements.
  $element['#tree'] = TRUE;

  // Normal FAPI field definitions, except that #value is defined.
  $element['areacode'] = array(
    '#type' => 'textfield',
    '#size' => 3,
    '#maxlength' => 3,
    '#required' => TRUE,
    '#prefix' => '(',
    '#suffix' => ')',
  );
  $element['prefix'] = array(
    '#type' => 'textfield',
    '#size' => 3,
    '#maxlength' => 3,
    '#required' => TRUE,
  );
  $element['extension'] = array(
    '#type' => 'textfield',
    '#size' => 4,
    '#maxlength' => 4,
    '#required' => TRUE,
  );

  $matches = array();
  $match = preg_match('/^(\d{3})(\d{3})(\d{4})$/', $element['#default_value'], $matches);
  if ($match) {
    // Get rid of the "all match" element.
    array_shift($matches);
    list($element['areacode']['#default_value'], $element['prefix']['#default_value'], $element['extension']['#default_value']) = $matches;
  }

  return $element;
}

/**
 * Phone number validation function for the combined phonenumber.
 *
 * Uses regular expressions to check that:
 *  - the area code is a three digit number
 *  - the prefix is numeric 3-digit number
 *  - the extension is a numeric 4-digit number
 *
 * Any problems are shown on the form element using form_error().
 *
 * The combined value is then updated in the element.
 */
function form_example_phonenumber_combined_validate($element, &$form_state) {
  $lengths = array(
    'areacode' => 3,
    'prefix' => 3,
    'extension' => 4,
  );
  foreach ($lengths as $member => $length) {
    $regex = '/^\d{' . $length . '}$/';
    if (!empty($element['#value'][$member]) && 0 == preg_match($regex, $element['#value'][$member])) {
      form_error($element[$member], t('@member is invalid', array('@member' => $member)));
    }
  }

  // Consolidate into the three parts into one combined value.
  $value = $element['areacode']['#value'] . $element['prefix']['#value'] . $element['extension']['#value'];
  form_set_value($element, $value, $form_state);
  return $element;
}

/**
 * Called by form_example_theme() to provide hook_theme().
 *
 * This is kept in this file so it can be with the theme functions it presents.
 * Otherwise it would get lonely.
 */
function _form_example_element_theme() {
  return array(
    'form_example_inline_form_element' => array(
      'render element' => 'element',
      'file' => 'form_example_elements.inc',
    ),
    'form_example_checkbox' => array(
      'render element' => 'element',
      'file' => 'form_example_elements.inc',
    ),
  );
}

/**
 * Themes a custom checkbox.
 *
 * This doesn't actually do anything, but is here to show that theming can
 * be done here.
 */
function theme_form_example_checkbox($variables) {
  $element = $variables['element'];
  return theme('checkbox', $element);
}

/**
 * Formats child form elements as inline elements.
 */
function theme_form_example_inline_form_element($variables) {
  $element = $variables['element'];

  // Add element #id for #type 'item'.
  if (isset($element['#markup']) && !empty($element['#id'])) {
    $attributes['id'] = $element['#id'];
  }
  // Add element's #type and #name as class to aid with JS/CSS selectors.
  $attributes['class'] = array('form-item');
  if (!empty($element['#type'])) {
    $attributes['class'][] = 'form-type-' . strtr($element['#type'], '_', '-');
  }
  if (!empty($element['#name'])) {
    $attributes['class'][] = 'form-item-' . strtr($element['#name'],
      array(
        ' ' => '-',
        '_' => '-',
        '[' => '-',
        ']' => '',
      )
    );
  }
  // Add a class for disabled elements to facilitate cross-browser styling.
  if (!empty($element['#attributes']['disabled'])) {
    $attributes['class'][] = 'form-disabled';
  }
  $output = '<div' . drupal_attributes($attributes) . '>' . "\n";

  // If #title is not set, we don't display any label or required marker.
  if (!isset($element['#title'])) {
    $element['#title_display'] = 'none';
  }
  $prefix = isset($element['#field_prefix']) ? '<span class="field-prefix">' . $element['#field_prefix'] . '</span> ' : '';
  $suffix = isset($element['#field_suffix']) ? ' <span class="field-suffix">' . $element['#field_suffix'] . '</span>' : '';

  switch ($element['#title_display']) {
    case 'before':
      $output .= ' ' . theme('form_element_label', $variables);
      $output .= ' ' . '<div class="container-inline">' . $prefix . $element['#children'] . $suffix . "</div>\n";
      break;

    case 'invisible':
    case 'after':
      $output .= ' ' . $prefix . $element['#children'] . $suffix;
      $output .= ' ' . theme('form_element_label', $variables) . "\n";
      break;

    case 'none':
    case 'attribute':
      // Output no label and no required marker, only the children.
      $output .= ' ' . $prefix . $element['#children'] . $suffix . "\n";
      break;
  }

  if (!empty($element['#description'])) {
    $output .= ' <div class="description">' . $element['#description'] . "</div>\n";
  }

  $output .= "</div>\n";

  return $output;
}

/**
 * Form content for examples/form_example/element_example.
 *
 * Simple form to demonstrate how to use the various new FAPI elements
 * we've defined.
 */
function form_example_element_demo_form($form, &$form_state) {
  $form['a_form_example_textfield'] = array(
    '#type' => 'form_example_textfield',
    '#title' => t('Form Example textfield'),
    '#default_value' => variable_get('form_example_textfield', ''),
    '#description' => t('form_example_textfield is a new type, but it is actually uses the system-provided functions of textfield'),
  );

  $form['a_form_example_checkbox'] = array(
    '#type' => 'form_example_checkbox',
    '#title' => t('Form Example checkbox'),
    '#default_value' => variable_get('form_example_checkbox', FALSE),
    '#description' => t('Nothing more than a regular checkbox but with a theme provided by this module.'),
  );

  $form['a_form_example_element_discrete'] = array(
    '#type' => 'form_example_phonenumber_discrete',
    '#title' => t('Discrete phone number'),
    '#default_value' => variable_get(
      'form_example_element_discrete',
      array(
        'areacode' => '999',
        'prefix' => '999',
        'extension' => '9999',
      )
    ),
    '#description' => t('A phone number : areacode (XXX), prefix (XXX) and extension (XXXX). This one uses a "discrete" element type, one which stores the three parts of the telephone number separately.'),
  );

  $form['a_form_example_element_combined'] = array(
    '#type' => 'form_example_phonenumber_combined',
    '#title' => t('Combined phone number'),
    '#default_value' => variable_get('form_example_element_combined', '0000000000'),
    '#description' => t('form_example_element_combined one uses a "combined" element type, one with a single 10-digit value which is broken apart when needed.'),
  );

  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Submit'),
  );

  return $form;
}

/**
 * Submit handler for form_example_element_demo_form().
 */
function form_example_element_demo_form_submit($form, &$form_state) {
  // Exclude unnecessary elements.
  unset($form_state['values']['submit'], $form_state['values']['form_id'], $form_state['values']['op'], $form_state['values']['form_token'], $form_state['values']['form_build_id']);

  foreach ($form_state['values'] as $key => $value) {
    variable_set($key, $value);
    drupal_set_message(
      t('%name has value %value',
        array(
          '%name' => $key,
          '%value' => print_r($value, TRUE),
        )
      )
    );
  }
}
