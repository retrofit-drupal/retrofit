<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Field\FieldTypePluginManagerInterface;

/**
 * @FieldType(
 *     id = "retrofit_field",
 *     deriver = "\Retrofit\Drupal\Plugin\Derivative\FieldTypeDeriver",
 * )
 */
final class FieldType extends FieldItemBase
{

    public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition)
    {
        // @todo map from schema.
        return [];
    }

    public static function schema(FieldStorageDefinitionInterface $field_definition)
    {
        $field_type_manager = \Drupal::service('plugin.manager.field.field_type');
        assert($field_type_manager instanceof FieldTypePluginManagerInterface);
        $definition = $field_type_manager->getDefinition($field_definition->getType());
        $provider = $definition['provider'];
        \Drupal::moduleHandler()->loadInclude($provider, 'install');
        $callable = $provider . '_field_schema';
        if (is_callable($callable)) {
            return $callable($field_definition);
        }
        return [];
    }

    public function validate()
    {
        $constraints =  parent::validate();
        $callable = $this->getPluginDefinition()['provider'] . '_field_validate';
        if (is_callable($callable)) {
            $callable(
                $this->getEntity()->getEntityTypeId(),
                $this->getEntity(),
                $this->getFieldDefinition(),
                $this->getFieldDefinition()->getFieldStorageDefinition(),
                $this->getLangcode(),
                // Oh no, is this for the FieldItemList !?
                [$this],
                $constraints
            );
        }
        return $constraints;
    }

    public function isEmpty(): bool
    {
        $callable = $this->getPluginDefinition()['provider'] . '_field_is_empty';
        if (!is_callable($callable)) {
            return parent::isEmpty();
        }
        return $callable($this, $this->getFieldDefinition());
    }
}
