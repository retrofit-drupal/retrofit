<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Plugin\Field\FieldType;

use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\TypedData\ComplexDataDefinitionInterface;
use Drupal\Core\TypedData\ComplexDataInterface;
use Drupal\Core\TypedData\TraversableTypedDataInterface;
use Drupal\Core\TypedData\TypedDataInterface;

final class DecoratedFieldItem implements FieldItemInterface, \IteratorAggregate, \ArrayAccess
{
    public function __construct(
        private readonly FieldItemInterface $inner,
    ) {
    }

    public function getDataDefinition(): ComplexDataDefinitionInterface
    {
        return $this->inner->getDataDefinition();
    }

    public function get($property_name): TypedDataInterface
    {
        return $this->inner->get($property_name);
    }

    public function set($property_name, $value, $notify = true): ComplexDataInterface
    {
        return$this->inner->set($property_name, $value, $notify);
    }

    public function getProperties($include_computed = false): array
    {
        return $this->inner->getProperties($include_computed);
    }

    public function toArray(): array
    {
        return $this->inner->toArray();
    }

    public function isEmpty(): bool
    {
        return $this->inner->isEmpty();
    }

    public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition)
    {
        // TODO: Implement propertyDefinitions() method.
        // @TODO can we get class from field definition to delegate this call?
    }

    public static function mainPropertyName()
    {
        return FieldItemBase::mainPropertyName();
    }

    public static function schema(FieldStorageDefinitionInterface $field_definition)
    {
        // TODO: Implement schema() method.
        // @TODO can we get class from field storage definition to delegate this call?
    }

    public function getEntity(): FieldableEntityInterface
    {
        return $this->inner->getEntity();
    }

    public function getLangcode(): string
    {
        return $this->inner->getLangcode();
    }

    public function getFieldDefinition(): FieldDefinitionInterface
    {
        return $this->inner->getFieldDefinition();
    }

    public function __get($property_name)
    {
        return $this->inner->__get($property_name);
    }

    public function __set($property_name, $value)
    {
        $this->inner->__set($property_name, $value);
    }

    public function __isset($property_name)
    {
        return $this->inner->__isset($property_name);
    }

    public function __unset($property_name)
    {
        $this->inner->__unset($property_name);
    }

    public function __call(string $name, array $arguments)
    {
        return call_user_func_array([$this->inner, $name], $arguments);
    }

    public function view($display_options = []): array
    {
        return $this->inner->view($display_options);
    }

    public function preSave(): void
    {
        $this->inner->preSave();
    }

    public function postSave($update)
    {
        // docs say it should return bool, but base class is a void.
        return $this->inner->postSave($update);
    }

    public function delete(): void
    {
        $this->inner->delete();
    }

    public function deleteRevision(): void
    {
        $this->inner->deleteRevision();
    }

    public static function generateSampleValue(FieldDefinitionInterface $field_definition)
    {
        // TODO: Implement generateSampleValue() method.
        // @TODO can we get class from field definition to delegate this call?
    }

    public static function defaultStorageSettings()
    {
        // TODO: Implement defaultStorageSettings() method.
        // @TODO can we get class from field definition to delegate this call?
    }

    public static function defaultFieldSettings()
    {
        // TODO: Implement defaultFieldSettings() method.
        // @TODO can we get class from field definition to delegate this call?
    }

    public static function storageSettingsSummary(FieldStorageDefinitionInterface $storage_definition): array
    {
        // TODO: Implement storageSettingsSummary() method.
        // @TODO can we get class from field definition to delegate this call?
        return [];
    }

    public static function fieldSettingsSummary(FieldDefinitionInterface $field_definition): array
    {
        // TODO: Implement fieldSettingsSummary() method.
        // @TODO can we get class from field definition to delegate this call?
        return [];
    }

    public static function storageSettingsToConfigData(array $settings)
    {
        // TODO: Implement storageSettingsToConfigData() method.
        // @TODO can we get class from field definition to delegate this call?
    }

    public static function storageSettingsFromConfigData(array $settings)
    {
        // TODO: Implement storageSettingsFromConfigData() method.
        // @TODO can we get class from field definition to delegate this call?
    }

    public static function fieldSettingsToConfigData(array $settings)
    {
        // TODO: Implement fieldSettingsToConfigData() method.
        // @TODO can we get class from field definition to delegate this call?
    }

    public static function fieldSettingsFromConfigData(array $settings)
    {
        // TODO: Implement fieldSettingsFromConfigData() method.
        // @TODO can we get class from field definition to delegate this call?
    }

    public function storageSettingsForm(array &$form, FormStateInterface $form_state, $has_data)
    {
        return $this->inner->storageSettingsForm($form, $form_state, $has_data);
    }

    public function fieldSettingsForm(array $form, FormStateInterface $form_state)
    {
        return $this->inner->fieldSettingsForm($form, $form_state);
    }

    public static function calculateDependencies(FieldDefinitionInterface $field_definition)
    {
        // TODO: Implement calculateDependencies() method.
        // @TODO can we get class from field definition to delegate this call?
    }

    public static function calculateStorageDependencies(FieldStorageDefinitionInterface $field_storage_definition)
    {
        // TODO: Implement calculateStorageDependencies() method.
        // @TODO can we get class from field definition to delegate this call?
    }

    public static function onDependencyRemoval(FieldDefinitionInterface $field_definition, array $dependencies)
    {
        // TODO: Implement onDependencyRemoval() method.
        // @TODO can we get class from field definition to delegate this call?
    }

    public function onChange($name)
    {
        $this->inner->onChange($name);
    }

    public static function createInstance($definition, $name = null, TraversableTypedDataInterface $parent = null)
    {
        // @TODO can we get class from field definition to delegate this call?
        throw new \RuntimeException(__CLASS__ . ' should not be created.');
    }

    public function getValue()
    {
        return $this->inner->getValue();
    }

    public function setValue($value, $notify = true)
    {
        return $this->inner->setValue($value, $notify);
    }

    public function getString()
    {
        return $this->inner->getString();
    }

    public function getConstraints()
    {
        return $this->inner->getConstraints();
    }

    public function validate()
    {
        return $this->inner->validate();
    }

    public function applyDefaultValue($notify = true)
    {
        $this->inner->applyDefaultValue($notify);
    }

    public function getName()
    {
        return $this->inner->getName();
    }

    public function getParent()
    {
        return $this->inner->getParent();
    }

    public function getRoot()
    {
        return $this->inner->getRoot();
    }

    public function getPropertyPath()
    {
        return $this->inner->getPropertyPath();
    }

    public function setContext($name = null, TraversableTypedDataInterface $parent = null)
    {
        $this->inner->setContext($name, $parent);
    }

    public function offsetExists(mixed $offset): bool
    {
        if (!is_string($offset)) {
            return false;
        }
        return array_key_exists($offset, $this->getProperties());
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset)->getValue();
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->get($offset)->setValue($value);
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->get($offset)->setValue(null);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Drupal\Core\TypedData\Plugin\DataType\Map::getIterator
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->getProperties());
    }
}
