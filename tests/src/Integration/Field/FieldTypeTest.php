<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Tests\Integration\Field;

use Drupal\Core\Field\FieldTypePluginManagerInterface;
use Drupal\field\Entity\FieldStorageConfig;
use Retrofit\Drupal\Tests\Integration\IntegrationTestCase;

final class FieldTypeTest extends IntegrationTestCase
{
    protected static $modules = ['system', 'field', 'entity_test'];

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

    public function testInstalledField(): void
    {
        $storage = FieldStorageConfig::create([
            'field_name' => 'field_rgb',
            'type' => 'retrofit_field:field_example_rgb',
            'entity_type' => 'entity_test',
            'cardinality' => 1,
        ]);
        $storage->save();
        self::assertEquals([
            'columns' => ['rgb' => ['type' => 'varchar', 'length' => 7, 'not null' => false]],
            'indexes' => ['rgb' => ['rgb']],
            'unique keys' => [],
            'foreign keys' => [],
        ], $storage->getSchema());
    }
}
