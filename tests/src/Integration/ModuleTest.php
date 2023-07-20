<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Tests\Integration;

final class ModuleTest extends IntegrationTestCase
{
    protected static $modules = ['system'];

    public function testModuleExists(): void
    {
        self::assertTrue(module_exists('system'));
        self::assertFalse(module_exists('user'));
    }
}
