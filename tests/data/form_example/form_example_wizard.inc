<?php

/**
 * @file
 * Extensible wizard form example.
 */

/**
 * Extensible wizard form example.
 *
 * This is an example of a multistep form using a wizard style. It will include
 * the 'Previous' and 'Next' buttons when required, and a 'Finish' button at the
 * last stage of the form submission.
 *
 * This example is an extensible skeleton that can include (even
 * programmatically) more steps. The demonstration form includes three steps,
 * each step having its own validation functions.
 *
 * How to extend this example:
 * - Steps are defined in the _form_example_steps() function. Include or alter
 *   the steps as you require.
 * - For each step, implement the corresponding 'form' function (see
 *   'form_example_wizard_personal_info' for the first step in this example.)
 *   Each step is a regular form, and the wizard collects all the values of the
 *   included forms.
 * - Optionally, you may include custom validation functions using the regular
 *   validation hook (formname_validate). The wizard uses these validation
 *   functions for each step.
 * - The most important customization step is to change the submit handler and
 *   do whatever you want with the collected information. In this case, the
 *   example just shows the collected values in the various steps.
 * @ingroup form_example
 */

/**
 * Returns the list of steps and their associated forms.
 *
 * This has been separated to clarify and easy the understanding of this
 * example. You should edit this function to include the steps your
 * wizard/multistep form requires.
 *
 * @return array
 *   List of steps and their forms.
 *
 * @ingroup form_example
 */
function _form_example_steps() {
  return array(
    1 => array(
      'form' => 'form_example_wizard_personal_info',
    ),
    2 => array(
      'form' => 'form_example_wizard_location_info',
    ),
    3 => array(
      'form' => 'form_example_wizard_other_info',
    ),
  );
}

/**
 * The primary formbuilder function for the wizard form.
 *
 * This is the form that you should call with drupal_get_form() from your code,
 * and it will include the rest of the step forms defined. You are not required
 * to change this function, as this will handle all the step actions for you.
 *
 * This form has two defined submit handlers to process the different steps:
 *  - Previous: handles the way to get back one step in the wizard.
 *  - Next:     handles each step form submission,
 *
 * The third handler, the finish button handler, is the default form_submit
 * handler used to process the information.
 *
 * You are not required to change the next or previous handlers, but you must
 * change the form_example_wizard_submit handler to perform the operations you
 * need on the collected information.
 *
 * @ingroup form_example
 */
function form_example_wizard($form, &$form_state) {

  // Initialize a description of the steps for the wizard.
  if (empty($form_state['step'])) {
    $form_state['step'] = 1;

    // This array contains the function to be called at each step to get the
    // relevant form elements. It will also store state information for each
    // step.
    $form_state['step_information'] = _form_example_steps();
  }
  $step = &$form_state['step'];
  drupal_set_title(t('Extensible Wizard: Step @step', array('@step' => $step)));

  // Call the function named in $form_state['step_information'] to get the
  // form elements to display for this step.
  $form = $form_state['step_information'][$step]['form']($form, $form_state);

  // Show the 'previous' button if appropriate. Note that #submit is set to
  // a special submit handler, and that we use #limit_validation_errors to
  // skip all complaints about validation when using the back button. The
  // values entered will be discarded, but they will not be validated, which
  // would be annoying in a "back" button.
  if ($step > 1) {
    $form['prev'] = array(
      '#type' => 'submit',
      '#value' => t('Previous'),
      '#name' => 'prev',
      '#submit' => array('form_example_wizard_previous_submit'),
      '#limit_validation_errors' => array(),
    );
  }

  // Show the Next button only if there are more steps defined.
  if ($step < count($form_state['step_information'])) {
    // The Next button should be included on every step.
    $form['next'] = array(
      '#type' => 'submit',
      '#value' => t('Next'),
      '#name' => 'next',
      '#submit' => array('form_example_wizard_next_submit'),
    );
  }
  else {
    // Just in case there are no more steps, we use the default submit handler
    // of the form wizard. Call this button Finish, Submit, or whatever you
    // want to show. When this button is clicked, the
    // form_example_wizard_submit handler will be called.
    $form['finish'] = array(
      '#type' => 'submit',
      '#value' => t('Finish'),
    );
  }

  // Include each validation function defined for the different steps.
  if (function_exists($form_state['step_information'][$step]['form'] . '_validate')) {
    $form['next']['#validate'] = array($form_state['step_information'][$step]['form'] . '_validate');
  }

  return $form;
}

/**
 * Submit handler for the "previous" button.
 *
 * This function:
 * - Stores away $form_state['values']
 * - Decrements the step counter
 * - Replaces $form_state['values'] with the values from the previous state.
 * - Forces form rebuild.
 *
 * You are not required to change this function.
 *
 * @ingroup form_example
 */
function form_example_wizard_previous_submit($form, &$form_state) {
  $current_step = &$form_state['step'];
  $form_state['step_information'][$current_step]['stored_values'] = $form_state['input'];
  if ($current_step > 1) {
    $current_step--;
    $form_state['values'] = $form_state['step_information'][$current_step]['stored_values'];
  }
  $form_state['rebuild'] = TRUE;
}

/**
 * Submit handler for the 'next' button.
 *
 * This function:
 * - Saves away $form_state['values']
 * - Increments the step count.
 * - Replace $form_state['values'] from the last time we were at this page
 *   or with array() if we haven't been here before.
 * - Force form rebuild.
 *
 * You are not required to change this function.
 *
 * @ingroup form_example
 */
function form_example_wizard_next_submit($form, &$form_state) {
  $current_step = &$form_state['step'];
  $form_state['step_information'][$current_step]['stored_values'] = $form_state['values'];

  if ($current_step < count($form_state['step_information'])) {
    $current_step++;
    if (!empty($form_state['step_information'][$current_step]['stored_values'])) {
      $form_state['values'] = $form_state['step_information'][$current_step]['stored_values'];
    }
    else {
      $form_state['values'] = array();
    }
    // Force rebuild with next step.
    $form_state['rebuild'] = TRUE;
    return;
  }
}

/**
 * The previous code was a 'skeleton' of a multistep wizard form. You are not
 * required to change a line on the previous code (apart from defining your own
 * steps in the _form_example_steps() function.
 *
 * All the code included from here is the content of the wizard, the steps of
 * the form.
 *
 * First, let's show the defined steps for the wizard example.
 * @ingroup form_example
 */

/**
 * Returns form elements for the 'personal info' page of the wizard.
 *
 * This is the first step of the wizard, asking for two textfields: first name
 * and last name.
 *
 * @ingroup form_example
 */
function form_example_wizard_personal_info($form, &$form_state) {
  $form = array();
  $form['first_name'] = array(
    '#type' => 'textfield',
    '#title' => t('First Name'),
    '#default_value' => !empty($form_state['values']['first_name']) ? $form_state['values']['first_name'] : '',
  );
  $form['last_name'] = array(
    '#type' => 'textfield',
    '#title' => t('Last Name'),
    '#default_value' => !empty($form_state['values']['last_name']) ? $form_state['values']['last_name'] : '',
  );
  return $form;
}

/**
 * Returns form elements for the 'location info' page of the wizard.
 *
 * This is the second step of the wizard. This step asks for a textfield value:
 * a City. This step also includes a validation declared later.
 *
 * @ingroup form_example
 */
function form_example_wizard_location_info($form, &$form_state) {
  $form = array();
  $form['city'] = array(
    '#type' => 'textfield',
    '#title' => t('City'),
    '#description' => t('Hint: Do not enter "San Francisco", and do not leave this out.'),
    '#required' => TRUE,
    '#default_value' => !empty($form_state['values']['city']) ? $form_state['values']['city'] : '',

  );
  return $form;
}

/**
 * Custom validation form for the 'location info' page of the wizard.
 *
 * This is the validation function for the second step of the wizard.
 * The city cannot be empty or be "San Francisco".
 *
 * @ingroup form_example
 */
function form_example_wizard_location_info_validate($form, &$form_state) {
  if ($form_state['values']['city'] == 'San Francisco') {
    form_set_error('city', t('You were warned not to enter "San Francisco"'));
  }
}

/**
 * Returns form elements for the 'other info' page of the wizard.
 *
 * This is the third and last step of the example wizard.
 *
 * @ingroup form_example
 */
function form_example_wizard_other_info($form, &$form_state) {
  $form = array();
  $form['aunts_name'] = array(
    '#type' => 'textfield',
    '#title' => t("Your first cousin's aunt's Social Security number"),
    '#default_value' => !empty($form_state['values']['aunts_name']) ? $form_state['values']['aunts_name'] : '',
  );
  return $form;
}

/**
 * Wizard form submit handler.
 *
 * This function:
 * - Saves away $form_state['values']
 * - Process all the form values.
 *
 * And now comes the magic of the wizard, the function that should handle all
 * the inputs from the user on each different step.
 *
 * This demonstration handler just do a drupal_set_message() with the
 * information collected on each different step of the wizard.
 *
 * @ingroup form_example
 */
function form_example_wizard_submit($form, &$form_state) {
  $current_step = &$form_state['step'];
  $form_state['step_information'][$current_step]['stored_values'] = $form_state['values'];

  // In this case we've completed the final page of the wizard, so process the
  // submitted information.
  drupal_set_message(t('This information was collected by this wizard:'));
  foreach ($form_state['step_information'] as $index => $value) {
    // Remove FAPI fields included in the values (form_token, form_id and
    // form_build_id. This is not required, you may access the values using
    // $value['stored_values'] but I'm removing them to make a more clear
    // representation of the collected information as the complete array will
    // be passed through drupal_set_message().
    unset($value['stored_values']['form_id']);
    unset($value['stored_values']['form_build_id']);
    unset($value['stored_values']['form_token']);

    // Now show all the values.
    drupal_set_message(t('Step @num collected the following values: <pre>@result</pre>',
      array(
        '@num' => $index,
        '@result' => print_r($value['stored_values'], TRUE),
      )
    ));
  }
}
