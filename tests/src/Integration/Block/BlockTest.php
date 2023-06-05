<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Tests\Integration\Block;

use Drupal\Core\Block\BlockPluginInterface;
use Retrofit\Drupal\Tests\Integration\IntegrationTestCase;

final class BlockTest extends IntegrationTestCase
{
    protected static $modules = ['system'];

    protected static function getTestModules(): array
    {
        return ['block_example'];
    }

    public function testDefinitions(): void
    {
        $blockManager = $this->container->get('plugin.manager.block');
        self::assertTrue($blockManager->hasDefinition('retrofit_block:example_configurable_text'));
        self::assertTrue($blockManager->hasDefinition('retrofit_block:example_empty'));
        self::assertTrue($blockManager->hasDefinition('retrofit_block:example_uppercase'));
    }

    public function testExampleConfigurableText(): void
    {
        $blockManager = $this->container->get('plugin.manager.block');
        $instance = $blockManager->createInstance('retrofit_block:example_configurable_text');
        self::assertInstanceOf(BlockPluginInterface::class, $instance);

        self::assertEquals(['user.roles'], $instance->getCacheContexts());
        self::assertEquals([], $instance->getCacheTags());
        self::assertEquals(-1, $instance->getCacheMaxAge());

        $build = $instance->build();
        $this->render($build);
        self::assertStringContainsString(
            'A default value. This block was created at',
            $this->getTextContent()
        );
    }
}
