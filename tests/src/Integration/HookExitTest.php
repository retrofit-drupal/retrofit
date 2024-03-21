<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Tests\Integration;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\TerminateEvent;

final class HookExitTest extends IntegrationTestCase
{
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
            $this->container->get('state')->get('retrofit_fixtures_exit')
        );
        $event = new TerminateEvent(
            $this->container->get('http_kernel'),
            Request::create('/'),
            new Response()
        );
        $this->container->get('event_dispatcher')->dispatch($event);
        self::assertEquals(
            1,
            $this->container->get('state')->get('retrofit_fixtures_exit')
        );
    }
}
