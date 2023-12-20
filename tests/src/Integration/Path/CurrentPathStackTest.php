<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Tests\Integration\Path;

use mglaman\DrupalTestHelpers\RequestTrait;
use Retrofit\Drupal\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Request;

final class CurrentPathStackTest extends IntegrationTestCase
{
    use RequestTrait;

    protected static $modules = [
        'system',
        'user',
    ];

    protected function tearDown(): void
    {
        // phpcs:ignore
        unset($_GET['q']);
    }

    public function testIntegration()
    {
        self::assertArrayNotHasKey('q', $_GET);
        $this->doRequest(Request::create('/user/login'));
        self::assertEquals(
            '/user/login',
            $this->container->get('path.current')->getPath()
        );
        self::assertArrayHasKey('q', $_GET);
        self::assertEquals('/user/login', $_GET['q']);
        $_GET['q'] = '/login';
        self::assertEquals(
            '/login',
            $this->container->get('path.current')->getPath()
        );
    }
}
