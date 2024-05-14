<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Tests\Integration;

final class FileClassAutoloaderTest extends IntegrationTestCase
{
    protected static $modules = [
        'system',
    ];

    protected static function getTestModules(): array
    {
        return ['retrofit_fixtures'];
    }

    public function testAutoload(): void
    {
        self::assertTrue(class_exists(\SomeClass::class));

    }

}
