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
use Drupal\Core\TypedData\OptionsProviderInterface;
use Drupal\Core\TypedData\PrimitiveInterface;
use Drupal\Core\TypedData\TraversableTypedDataInterface;
use Drupal\Core\TypedData\TypedDataInterface;
use Drupal\Core\Validation\Plugin\Validation\Constraint\AllowedValuesConstraint;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * @implements  \ArrayAccess<string, mixed>
 * @implements  \IteratorAggregate<string, \Drupal\Core\TypedData\TypedDataInterface>
 */
final class DecoratedFieldItem implements FieldItemInterface, PrimitiveInterface, \IteratorAggregate, \ArrayAccess
{
    public function __construct(
        private readonly FieldItemInterface $inner,
    ) {
    }

    public function getDataDefinition(): ComplexDataDefinitionInterface
    {
        return $this->inner->getDataDefinition();
    }

    /**
     * @param string $property_name
     */
    public function get($property_name): TypedDataInterface
    {
        return $this->inner->get($property_name);
    }

    /**
     * @param string $property_name
     * @param mixed $value
     * @param bool $notify
     */
    public function set($property_name, $value, $notify = true): ComplexDataInterface
    {
        $this->inner->set($property_name, $value, $notify);
        return $this;
    }

    /**
     * @param bool $include_computed
     * @return array<string, \Drupal\Core\TypedData\TypedDataInterface>
     */
    public function getProperties($include_computed = false): array
    {
        return $this->inner->getProperties($include_computed);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->inner->toArray();
    }

    public function isEmpty(): bool
    {
        return $this->inner->isEmpty();
    }

    public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition): array
    {
        // TODO: Implement propertyDefinitions() method.
        // @TODO can we get class from field definition to delegate this call?
        return [];
    }

    public static function mainPropertyName(): ?string
    {
        return FieldItemBase::mainPropertyName();
    }

    public static function schema(FieldStorageDefinitionInterface $field_definition): array
    {
        // TODO: Implement schema() method.
        // @TODO can we get class from field storage definition to delegate this call?
        return [];
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

    public function __call(string $name, array $arguments): mixed
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
        return $settings;
    }

    public static function storageSettingsFromConfigData(array $settings)
    {
        // TODO: Implement storageSettingsFromConfigData() method.
        // @TODO can we get class from field definition to delegate this call?
        return $settings;
    }

    public static function fieldSettingsToConfigData(array $settings)
    {
        // TODO: Implement fieldSettingsToConfigData() method.
        // @TODO can we get class from field definition to delegate this call?
        return $settings;
    }

    public static function fieldSettingsFromConfigData(array $settings)
    {
        // TODO: Implement fieldSettingsFromConfigData() method.
        // @TODO can we get class from field definition to delegate this call?
        return $settings;
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
        return [];
    }

    public static function calculateStorageDependencies(
        FieldStorageDefinitionInterface $field_storage_definition
    ): array {
        // TODO: Implement calculateStorageDependencies() method.
        // @TODO can we get class from field definition to delegate this call?
        return [];
    }

    public static function onDependencyRemoval(FieldDefinitionInterface $field_definition, array $dependencies): bool
    {
        // TODO: Implement onDependencyRemoval() method.
        // @TODO can we get class from field definition to delegate this call?
        return false;
    }

    /**
     * @param string $name
     */
    public function onChange($name): void
    {
        $this->inner->onChange($name);
    }

    /**
     * @param array<string, mixed> $definition
     * @param string $name
     * @param TraversableTypedDataInterface|null $parent
     */
    public static function createInstance($definition, $name = null, TraversableTypedDataInterface $parent = null): void
    {
        // @TODO can we get class from field definition to delegate this call?
        throw new \RuntimeException(__CLASS__ . ' should not be created.');
    }

    public function getValue(): mixed
    {
        return $this->inner->getValue();
    }

    /**
     * @param mixed|null $value
     * @param bool $notify
     */
    public function setValue($value, $notify = true): void
    {
        $this->inner->setValue($value, $notify);
    }

    public function getCastedValue(): mixed
    {
        $name = $this->inner->getDataDefinition()->getMainPropertyName();
        if (!isset($name)) {
            throw new \LogicException('Cannot validate allowed values for complex data without a main property.');
        }
        $typed_data = $this->inner->get($name);
        if (method_exists($typed_data, 'getCastedValue')) {
            $value = $typed_data->getCastedValue();
        } else {
            $value =  $typed_data->getValue();
        }
        return $value;
    }

    public function getString(): string
    {
        return $this->inner->getString();
    }

    /**
     * @return array<int, Constraint>
     */
    public function getConstraints(): array
    {
        $constraints = [];
        foreach ($this->inner->getConstraints() as $constraint) {
            if ($this->inner instanceof OptionsProviderInterface && $constraint instanceof AllowedValuesConstraint) {
                $allowed_values = $this->inner->getSettableValues(\Drupal::currentUser());
                $constraint->choices = $allowed_values;
            }
            $constraints[] = $constraint;
        }
        return $constraints;
    }

    public function validate(): ConstraintViolationListInterface
    {
        return $this->inner->validate();
    }

    public function applyDefaultValue($notify = true): FieldItemInterface
    {
        $this->inner->applyDefaultValue($notify);
        return $this;
    }

    public function getName(): int|null|string
    {
        return $this->inner->getName();
    }

    public function getParent(): ?TraversableTypedDataInterface
    {
        return $this->inner->getParent();
    }

    public function getRoot(): TraversableTypedDataInterface
    {
        return $this->inner->getRoot();
    }

    public function getPropertyPath(): string
    {
        return $this->inner->getPropertyPath();
    }

    /**
     * @param string|null $name
     */
    public function setContext($name = null, TraversableTypedDataInterface $parent = null): void
    {
        $this->inner->setContext($name, $parent);
    }

    public function offsetExists(mixed $offset): bool
    {
        settype($offset, 'string');
        return isset($this->inner->$offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        settype($offset, 'string');
        return $this->inner->$offset;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        settype($offset, 'string');
        $this->inner->$offset = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        settype($offset, 'string');
        unset($this->inner->$offset);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Drupal\Core\TypedData\Plugin\DataType\Map::getIterator
     * @return \ArrayIterator<string, \Drupal\Core\TypedData\TypedDataInterface>
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->getProperties());
    }
}
