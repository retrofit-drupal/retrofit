<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Tests\Integration;

use Drupal\Core\Routing\RouteBuilderInterface;
use Drupal\Core\State\StateInterface;
use Drupal\KernelTests\KernelTestBase;

final class VariablesTest extends KernelTestBase
{
    public function testSet(): void
    {
        $state = $this->container->get('state');
        self::assertInstanceOf(StateInterface::class, $state);
        variable_set('foobar', 'baz');
        self::assertEquals('baz', $state->get('foobar'));

        $route_builder = $this->container->get('router.builder');
        self::assertInstanceOf(RouteBuilderInterface::class, $route_builder);
        variable_set('menu_rebuild_needed', true);
        self::assertTrue($route_builder->rebuildIfNeeded());
    }

    public function testDelete(): void
    {
        $state = $this->container->get('state');
        self::assertInstanceOf(StateInterface::class, $state);
        variable_set('foobar', 'baz');
        self::assertEquals('baz', $state->get('foobar'));
        variable_del('foobar');
        self::assertNull($state->get('foobar'));
    }

    public function testGet(): void
    {
        variable_set('foobar', 'baz');
        self::assertEquals('baz', variable_get('foobar'));
    }
}
