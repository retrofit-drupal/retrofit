<?php

declare(strict_types=1);

use Drupal\Component\Utility\NestedArray;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Field\FieldFilteredMarkup;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\FieldConfigInterface;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\field\FieldStorageConfigInterface;

function _field_sort_items_value_helper(mixed $a, mixed $b): float
{
    return _field_multiple_value_form_sort_helper($a, $b);
}

function field_filter_xss(string $string): string
{
    return Xss::filter($string, FieldFilteredMarkup::allowedTags());
}

function field_form_get_state(array $parents, string $field_name, string $langcode, array &$form_state): ?array
{
    return NestedArray::getValue($form_state, array_merge([
        'storage',
        'field_storage',
        '#parents',
    ], $parents, [
        '#fields',
        $field_name,
    ]));
}

/**
 * @param array{
 *   field_name: string,
 *   type: string
 * } $field
 */
function field_create_field(array $field): FieldStorageConfigInterface
{
    $field_type_manager = \Drupal::service('plugin.manager.field.field_type');
    $info = $field_type_manager->getDefinitions();
    $field['type'] = match ($field['type']) {
        'datestamp' => 'datetime',
        'list_text' => 'list_string',
        default => isset($info["retrofit_field:$field[type]"]) ? "retrofit_field:$field[type]" : $field['type'],
    };
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

function field_info_field(string $field_name): ?FieldStorageConfigInterface
{
    return FieldStorageConfig::loadByName('node', $field_name);
}

function field_info_instance(string $entity_type, string $field_name, string $bundle_name): ?FieldConfigInterface
{
    return FieldConfig::loadByName($entity_type, $bundle_name, $field_name);
}
