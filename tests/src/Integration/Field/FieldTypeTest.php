<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Tests\Integration\Field;

use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\Sql\DefaultTableMapping;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Field\FieldTypePluginManagerInterface;
use Drupal\entity_test\Entity\EntityTest;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Retrofit\Drupal\Tests\Integration\IntegrationTestCase;

final class FieldTypeTest extends IntegrationTestCase
{
    /**
     * @var string[]
     */
    protected static $modules = ['user', 'system', 'field', 'entity_test'];

    protected static function getTestModules(): array
    {
        return ['field_example'];
    }

    public function testDefinitions(): void
    {
        $fieldTypeManager = $this->container->get('plugin.manager.field.field_type');
        assert($fieldTypeManager instanceof FieldTypePluginManagerInterface);
        self::assertTrue($fieldTypeManager->hasDefinition('retrofit_field:field_example_rgb'));
    }

    public function testSchema(): void
    {
        [$storage, ] = $this->createField('field_rgb', 'retrofit_field:field_example_rgb', 1);
        self::assertEquals([
            'columns' => ['rgb' => ['type' => 'varchar', 'length' => 7, 'not null' => false]],
            'indexes' => ['rgb' => ['rgb']],
            'unique keys' => [],
            'foreign keys' => [],
        ], $storage->getSchema());
    }

    public function testProperties(): void
    {
        [$storage, ] = $this->createField('field_rgb', 'retrofit_field:field_example_rgb', 1);
        $property = $storage->getPropertyDefinition('rgb');
        self::assertNotNull($property);
        self::assertEquals('string', $property->getDataType());
    }

    public function testIsEmpty(): void
    {
        $this->createField('field_rgb', 'retrofit_field:field_example_rgb', 1);
        $entity = EntityTest::create();
        self::assertTrue($entity->get('field_rgb')->isEmpty());
        $entity->get('field_rgb')->setValue('#000000');
        self::assertFalse($entity->get('field_rgb')->isEmpty());
    }

    public function testValidate(): void
    {
        $this->createField('field_rgb', 'retrofit_field:field_example_rgb', 1);
        $entity = EntityTest::create();
        $entity->get('field_rgb')->setValue('invalid');
        $violations = $entity->get('field_rgb')->validate();
        self::assertCount(1, $violations);
        self::assertEquals(
            'Color must be in the HTML format #abcdef.',
            (string) $violations->get(0)->getMessage()
        );
        $entity->get('field_rgb')->setValue('#000000');
        $violations = $entity->get('field_rgb')->validate();
        self::assertCount(0, $violations);
    }

    public function testSavingEntity(): void
    {
        $this->installEntitySchema('entity_test');
        $this->createField('field_rgb', 'retrofit_field:field_example_rgb', 1);
        $entity = EntityTest::create();
        $entity->get('field_rgb')->setValue('#000000');
        $entity->save();

        $database = $this->container->get('database');
        self::assertInstanceOf(Connection::class, $database);
        $schema = $database->schema();

        self::assertTrue($schema->tableExists('entity_test__field_rgb'));
        self::assertTrue($schema->fieldExists('entity_test__field_rgb', 'field_rgb_rgb'));

        $values = $database->select('entity_test__field_rgb')
            ->fields('entity_test__field_rgb', [])
            ->execute()
            ->fetchAll();
        self::assertEquals('#000000', $values[0]->field_rgb_rgb);
    }


    /**
     * @return array{0: FieldStorageDefinitionInterface, 1: FieldDefinitionInterface}
     */
    private function createField(string $name, string $type, int $cardinality): array
    {
        $storage = FieldStorageConfig::create([
            'field_name' => $name,
            'type' => $type,
            'entity_type' => 'entity_test',
            'cardinality' => $cardinality,
        ]);
        $storage->save();
        $field = FieldConfig::create([
            'field_storage' => $storage,
            'bundle' => 'entity_test',
            'required' => true,
        ]);
        $field->save();
        return [$storage, $field];
    }
}
