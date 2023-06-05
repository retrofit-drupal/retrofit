<?php

/**
 * @file
 * An example of how to use the new #states Form API element, allowing
 * dynamic form behavior with very simple setup.
 */

/**
 * States demo form.
 *
 * This form shows off the #states system by dynamically showing parts of the
 * form based on the state of other parts.
 *
 * @ingroup form_example
 *
 * The basic idea is that you add a #states property to the element which is
 * to be changed based on some action elsewhere on the form. The #states
 * property lists a change which is to be made, and under what conditions
 * that change should be made.
 *
 * For example, in the 'tests_taken' form element below we have:
 * @code
 * '#states' => array(
 *   'visible' => array(
 *     ':input[name="student_type"]' => array('value' => 'high_school'),
 *   ),
 * ),
 * @endcode
 * Meaning that the element is to be made visible when the condition is met.
 * The condition is a combination of a jQuery selector (which selects the
 * element we want to test) and a condition for that element. In this case,
 * the condition is whether the return value of the 'student_type' element is
 * 'high_school'. If it is, this element will be visible.
 *
 * So the syntax is:
 * @code
 * '#states' => array(
 *   'action_to_take_on_this_form_element' => array(
 *     'jquery_selector_for_another_element' => array(
 *       'condition_type' => value,
 *     ),
 *   ),
 * ),
 * @endcode
 *
 * If you need an action to take place only when two different conditions are
 * true, then you add both of those conditions to the action. See the
 * 'country_writein' element below for an example.
 *
 * Note that the easiest way to select a textfield, checkbox, or select is with
 * the
 * @link http://api.jquery.com/input-selector/ ':input' jquery shortcut @endlink,
 * which selects any any of those.
 *
 * There are examples below of changing or hiding an element when a checkbox
 * is checked, when a textarea is filled, when a select has a given value.
 *
 * See drupal_process_states() for full documentation.
 *
 * @see forms_api_reference.html
 */
function form_example_states_form($form, &$form_state) {
  $form['student_type'] = array(
    '#type' => 'radios',
    '#options' => array(
      'high_school'   => t('High School'),
      'undergraduate' => t('Undergraduate'),
      'graduate'      => t('Graduate'),
    ),
    '#title' => t('What type of student are you?'),
  );
  $form['high_school'] = array(
    '#type' => 'fieldset',
    '#title' => t('High School Information'),
    // This #states rule says that the "high school" fieldset should only
    // be shown if the "student_type" form element is set to "High School".
    '#states' => array(
      'visible' => array(
        ':input[name="student_type"]' => array('value' => 'high_school'),
      ),
    ),
  );

  // High school information.
  $form['high_school']['tests_taken'] = array(
    '#type' => 'checkboxes',
    '#options' => array(
      'SAT' => t('SAT'),
      'ACT' => t('ACT')
    ),
    '#title' => t('What standardized tests did you take?'),
    // This #states rule says that this checkboxes array will be visible only
    // when $form['student_type'] is set to t('High School').
    // It uses the jQuery selector :input[name=student_type] to choose the
    // element which triggers the behavior, and then defines the "High School"
    // value as the one that triggers visibility.
    '#states' => array(
      // Action to take.
      'visible' => array(
        ':input[name="student_type"]' => array('value' => 'high_school'),
      ),
    ),
  );

  $form['high_school']['sat_score'] = array(
    '#type' => 'textfield',
    '#title' => t('Your SAT score:'),
    '#size' => 4,

    // This #states rule limits visibility to when the $form['tests_taken']
    // 'SAT' checkbox is checked."
    '#states' => array(
      // Action to take.
      'visible' => array(
        ':input[name="tests_taken[SAT]"]' => array('checked' => TRUE),
      ),
    ),
  );
  $form['high_school']['act_score'] = array(
    '#type' => 'textfield',
    '#title' => t('Your ACT score:'),
    '#size' => 4,

    // Set this element visible if the ACT checkbox above is checked.
    '#states' => array(
      // Action to take.
      'visible' => array(
        ':input[name="tests_taken[ACT]"]' => array('checked' => TRUE),
      ),
    ),
  );

  // Undergrad information.
  $form['undergraduate'] = array(
    '#type' => 'fieldset',
    '#title' => t('Undergraduate Information'),
    // This #states rule says that the "undergraduate" fieldset should only
    // be shown if the "student_type" form element is set to "Undergraduate".
    '#states' => array(
      'visible' => array(
        ':input[name="student_type"]' => array('value' => 'undergraduate'),
      ),
    ),
  );

  $form['undergraduate']['how_many_years'] = array(
    '#type' => 'select',
    '#title' => t('How many years have you completed?'),
    // The options here are integers, but since all the action here happens
    // using the DOM on the client, we will have to use strings to work with
    // them.
    '#options' => array(
      1 => t('One'),
      2 => t('Two'),
      3 => t('Three'),
      4 => t('Four'),
      5 => t('Lots'),
    ),
  );

  $form['undergraduate']['comment'] = array(
    '#type' => 'item',
    '#description' => t("Wow, that's a long time."),
    '#states' => array(
      'visible' => array(
        // Note that '5' must be used here instead of the integer 5.
        // The information is coming from the DOM as a string.
        ':input[name="how_many_years"]' => array('value' => '5'),
      ),
    ),
  );
  $form['undergraduate']['school_name'] = array(
    '#type' => 'textfield',
    '#title' => t('Your college or university:'),
  );
  $form['undergraduate']['school_country'] = array(
    '#type' => 'select',
    '#options' => array(
      'UK' => t('UK'),
      'other' => t('Other'),
    ),
    '#title' => t('In what country is your college or university located?'),
  );
  $form['undergraduate']['country_writein'] = array(
    '#type' => 'textfield',
    '#size' => 20,
    '#title' => t('Please enter the name of the country where your college or university is located.'),

    // Only show this field if school_country is set to 'Other'.
    '#states' => array(
      // Action to take: Make visible.
      'visible' => array(
        ':input[name="school_country"]' => array('value' => t('Other')),
      ),
    ),
  );

  $form['undergraduate']['thanks'] = array(
    '#type' => 'item',
    '#description' => t('Thanks for providing both your school and your country.'),
    '#states' => array(
      // Here visibility requires that two separate conditions be true.
      'visible' => array(
        ':input[name="school_country"]' => array('value' => t('Other')),
        ':input[name="country_writein"]' => array('filled' => TRUE),
      ),
    ),
  );
  $form['undergraduate']['go_away'] = array(
    '#type' => 'submit',
    '#value' => t('Done with form'),
    '#states' => array(
      // Here visibility requires that two separate conditions be true.
      'visible' => array(
        ':input[name="school_country"]' => array('value' => t('Other')),
        ':input[name="country_writein"]' => array('filled' => TRUE),
      ),
    ),
  );

  // Graduate student information.
  $form['graduate'] = array(
    '#type' => 'fieldset',
    '#title' => t('Graduate School Information'),
    // This #states rule says that the "graduate" fieldset should only
    // be shown if the "student_type" form element is set to "Graduate".
    '#states' => array(
      'visible' => array(
        ':input[name="student_type"]' => array('value' => 'graduate'),
      ),
    ),
  );
  $form['graduate']['more_info'] = array(
    '#type' => 'textarea',
    '#title' => t('Please describe your graduate studies'),
  );

  $form['graduate']['info_provide'] = array(
    '#type' => 'checkbox',
    '#title' => t('Check here if you have provided information above'),
    '#disabled' => TRUE,
    '#states' => array(
      // Mark this checkbox checked if the "more_info" textarea has something
      // in it, if it's 'filled'.
      'checked' => array(
        ':input[name="more_info"]' => array('filled' => TRUE),
      ),
    ),
  );

  $form['average'] = array(
    '#type' => 'textfield',
    '#title' => t('Enter your average'),
    // To trigger a state when the same controlling element can have more than
    // one possible value, put all values in a higher-level array.
    '#states' => array(
      'visible' => array(
        ':input[name="student_type"]' => array(
          array('value' => 'high_school'),
          array('value' => 'undergraduate'),
        ),
      ),
    ),
  );

  $form['expand_more_info'] = array(
    '#type' => 'checkbox',
    '#title' => t('Check here if you want to add more information.'),
  );
  $form['more_info'] = array(
    '#type' => 'fieldset',
    '#title' => t('Additional Information'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,

    // Expand the expand_more_info fieldset if the box is checked.
    '#states' => array(
      'expanded' => array(
        ':input[name="expand_more_info"]' => array('checked' => TRUE),
      ),
    ),
  );
  $form['more_info']['feedback'] = array(
    '#type' => 'textarea',
    '#title' => t('What do you have to say?'),
  );

  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Submit your information'),
  );

  return $form;
}

/**
 * Submit handler for form_example_states_form().
 */
function form_example_states_form_submit($form, &$form_state) {
  drupal_set_message(t('Submitting values: @values', array('@values' => var_export($form_state['values'], TRUE))));
}
