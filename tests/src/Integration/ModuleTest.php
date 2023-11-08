<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Tests\Integration;

use Drupal\Core\Extension\ModuleHandlerInterface;

final class ModuleTest extends IntegrationTestCase
{
    protected static $modules = ['system'];

    public function testModuleExists(): void
    {
        self::assertTrue(module_exists('system'));
        self::assertFalse(module_exists('user'));
    }

    public function testModuleList(): void
    {
        self::assertSame([
            'sqlite' => 'sqlite',
            'system' => 'system',
        ], module_list());
        self::assertSame([
            'sqlite' => 'sqlite',
            'system' => 'system',
        ], module_list(true, true, true));

        self::assertSame([
            'user' => 'user',
            'system' => 'system',
            'sqlite' => 'sqlite',
        ], module_list(true, true, false, [
            'user' => ['filename' => 'core/modules/user/user.module'],
            'system' => ['filename' => 'core/modules/system/system.module'],
            'sqlite' => ['filename' => 'core/modules/sqlite/sqlite.module'],
        ]));

        $moduleHandler = $this->container->get('module_handler');
        self::assertInstanceOf(ModuleHandlerInterface::class, $moduleHandler);
        self::assertEquals(
            ['user', 'system', 'sqlite'],
            array_keys($moduleHandler->getModuleList())
        );
    }
}
