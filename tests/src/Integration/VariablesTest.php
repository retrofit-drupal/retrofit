<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Tests\Integration;

use Drupal\Core\Routing\RouteBuilderInterface;
use Drupal\Core\State\StateInterface;
use Drupal\KernelTests\KernelTestBase;

final class VariablesTest extends KernelTestBase
{
    protected static $modules = ['system'];

    public function testSet(): void
    {
        $state = $this->container->get('state');
        variable_set('foobar', 'baz');
        self::assertEquals('baz', $state->get('foobar'));

        $route_builder = $this->container->get('router.builder');
        variable_set('menu_rebuild_needed', true);
        self::assertTrue($route_builder->rebuildIfNeeded());
    }

    public function testDelete(): void
    {
        $state = $this->container->get('state');
        variable_set('foobar', 'baz');
        self::assertEquals('baz', $state->get('foobar'));
        variable_del('foobar');
        self::assertNull($state->get('foobar'));
    }

    public function testGet(): void
    {
        variable_set('foobar', 'baz');
        self::assertEquals('baz', variable_get('foobar'));

        $this->config('system.site')
            ->set('name', 'Drupal')
            ->set('slogan', 'With Retrofit')
            ->save();
        self::assertEquals('Drupal', variable_get('site_name'));
        self::assertEquals('With Retrofit', variable_get('site_slogan'));
    }
}
