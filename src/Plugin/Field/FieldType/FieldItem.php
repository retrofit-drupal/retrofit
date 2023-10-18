<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\field\FieldStorageConfigInterface;

/**
 * @FieldType(
 *     id = "retrofit_field",
 *     deriver = "\Retrofit\Drupal\Plugin\Derivative\FieldItemDeriver",
 *     list_class = "\Retrofit\Drupal\Plugin\Field\FieldType\FieldItemList"
 * )
 */
final class FieldItem extends FieldItemBase
{
    public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition)
    {
        $properties = [];
        foreach (self::schema($field_definition)['columns'] as $column => $settings) {
            $type = match ($settings['type']) {
                'blob' => 'string_long',
                'char' => 'string',
                'float' => 'float',
                'int' => 'integer',
                'numeric' => 'string',
                'serial' => 'integer',
                'text' => 'string',
                'varchar' => 'string',
                default => throw new \RuntimeException(
                    "Could not determine field property definition for column $column of type {$settings['type']}"
                ),
            };
            $properties[$column] = DataDefinition::create($type)
                ->setLabel(new TranslatableMarkup($column))
                ->setRequired(true);
        }
        return $properties;
    }

    public static function schema(FieldStorageDefinitionInterface $field_definition): array
    {
        if ($field_definition instanceof FieldStorageConfigInterface) {
            $provider = $field_definition->getTypeProvider();
        } else {
            $provider = $field_definition->getProvider();
        }
        \Drupal::moduleHandler()->loadInclude($provider, 'install');
        $callable = $provider . '_field_schema';
        if (is_callable($callable)) {
            return $callable($field_definition);
        }
        return [];
    }

    public static function mainPropertyName()
    {
        // @todo find a way to return the main property from schema, without invoking the hook directly.
        //   maybe the deriver invokes it and sets schema in the plugin definition.
        return parent::mainPropertyName();
    }

    public function isEmpty(): bool
    {
        $callable = $this->getPluginDefinition()['provider'] . '_field_is_empty';
        if (!is_callable($callable)) {
            return parent::isEmpty();
        }
        return $callable(new DecoratedFieldItem($this), $this->getFieldDefinition());
    }
}
