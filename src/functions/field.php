<?php

declare(strict_types=1);

use Drupal\field\Entity\FieldConfig;
use Drupal\field\FieldConfigInterface;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\field\FieldStorageConfigInterface;

/**
 * @param array{
 *   field_name: string,
 *   type: string
 * } $field
 */
function field_create_field(array $field): FieldStorageConfigInterface
{
    $info = drupal_static('retrofit_field_info');
    if (!isset($info)) {
        $info = [];
        \Drupal::moduleHandler()->invokeAllWith(
            'field_info',
            function (callable $hook, string $module) use (&$info): void {
                $info += $hook();
            }
        );
    }
    assert(is_array($info));
    if (isset($info[$field['type']])) {
        $field['type'] = "retrofit_field:$field[type]";
    }
    $field_storage = FieldStorageConfig::create($field + ['entity_type' => 'node']);
    $field_storage->save();
    return $field_storage;
}

/**
 * @param array{
 *   field_name: string,
 *   entity_type: string,
 *   bundle: string
 * } $instance
 */
function field_create_instance(array $instance): FieldConfigInterface
{
    $field = FieldConfig::create($instance);
    $field->save();
    return $field;
}
