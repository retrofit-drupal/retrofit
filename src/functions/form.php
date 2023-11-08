<?php

declare(strict_types=1);

use Drupal\Core\Form\FormStateInterface;
use Retrofit\Drupal\Form\DrupalGetForm;
use Retrofit\Drupal\Form\ArrayAccessFormState;

/**
 * @param mixed[] $form_state
 * @return mixed[]
 */
function drupal_build_form(string $form_id, array &$form_state): array
{
    $form_object = \Drupal::classResolver(DrupalGetForm::class);
    $form_object->setFormId($form_id);
    return \Drupal::formBuilder()->buildForm($form_object, $form_state);
}

function drupal_form_submit(string $form_id, FormStateInterface $form_state): void
{
    \Drupal::formBuilder()->submitForm($form_id, $form_state);
}

/**
 * @return mixed[]
 */
function drupal_get_form(string $form_id): array
{
    $form_object = \Drupal::classResolver(DrupalGetForm::class);
    $form_object->setFormId($form_id);
    return \Drupal::formBuilder()->getForm($form_object);
}

/**
 * @param mixed[] $element
 */
function form_error(array &$element, string $message = ''): void
{
    form_set_error(implode('][', (array) $element['#parents']), $message);
}

function form_load_include(
    FormStateInterface &$form_state,
    string $type,
    string $module,
    ?string $name = null
): string|false {
    return $form_state->loadInclude($module, $type, $name);
}

/**
 * @param ?string[] $limit_validation_errors
 * @return mixed[]
 */
function form_set_error(?string $name = null, string $message = '', ?array $limit_validation_errors = null): array
{
    // @todo Find a way to get form state to this really works.
    $form = &drupal_static(__FUNCTION__, []);
    $form = (array) $form;
    $sections = &drupal_static(__FUNCTION__ . ':limit_validation_errors');
    if (isset($limit_validation_errors)) {
        $sections = $limit_validation_errors;
    }
    if (isset($name) && !isset($form[$name])) {
        $record = true;
        if (is_array($sections)) {
            $record = false;
            foreach ($sections as $section) {
                if (
                    array_slice(explode('][', $name), 0, count((array) $section))
                    === array_map('strval', (array) $section)
                ) {
                    $record = true;
                    break;
                }
            }
        }
        if ($record) {
            $form[$name] = $message;
        }
    }
    return $form;
}

/**
 * @param mixed[] $element
 */
function form_set_value(array $element, mixed $value, FormStateInterface &$form_state): void
{
    $form_state->setValueForElement($element, $value);
}

function form_state_values_clean(FormStateInterface $form_state): void
{
    $form_state->cleanValues();
}

function form_get_errors()
{
    $form = form_set_error();
    if (!empty($form)) {
        return $form;
    }
}
