<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Tests\Integration;

use mglaman\DrupalTestHelpers\RequestTrait;
use Symfony\Component\HttpFoundation\Request;

final class HookInitTest extends IntegrationTestCase
{
    use RequestTrait;

    protected static $modules = [
        'system',
    ];

    protected static function getTestModules(): array
    {
        return ['retrofit_fixtures'];
    }

    public function testHook(): void
    {
        self::assertEquals(
            0,
            $this->container->get('state')->get('retrofit_fixtures_init')
        );
        $this->doRequest(Request::create('/retrofit/drupal_set_title'));
        self::assertEquals(
            1,
            $this->container->get('state')->get('retrofit_fixtures_init')
        );
    }
}
