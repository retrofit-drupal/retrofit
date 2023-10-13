<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Tests\Integration\Field;

use Drupal\Core\Field\FormatterPluginManager;
use Retrofit\Drupal\Tests\Integration\IntegrationTestCase;

final class FieldFormatterTest extends IntegrationTestCase
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
        $fieldFormatterManager = $this->container->get('plugin.manager.field.formatter');
        assert($fieldFormatterManager instanceof FormatterPluginManager);
        self::assertTrue(
            $fieldFormatterManager->hasDefinition('retrofit_field_formatter:field_example_simple_text')
        );
        self::assertTrue(
            $fieldFormatterManager->hasDefinition('retrofit_field_formatter:field_example_color_background')
        );

        self::assertEquals(
            [
                'retrofit_field_formatter:field_example_simple_text',
                'retrofit_field_formatter:field_example_color_background',
            ],
            array_keys($fieldFormatterManager->getOptions('retrofit_field:field_example_rgb'))
        );
    }
}
