<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Tests\Unit\Routing;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Routing\RouteBuilderInterface;
use Drupal\Core\State\StateInterface;
use PHPUnit\Framework\TestCase;

final class MenuRebuildNeededTest extends TestCase
{
    public function testVariableSetTriggersMenuRebuild(): void
    {
        $container = new ContainerBuilder();

        $state = $this->createMock(StateInterface::class);
        $state->expects($this->never())->method('set');
        $container->set('state', $state);

        $router_builder = $this->createMock(RouteBuilderInterface::class);
        $router_builder->expects($this->once())->method('setRebuildNeeded');
        $container->set('router.builder', $router_builder);

        \Drupal::setContainer($container);

        variable_set('menu_rebuild_needed', true);
    }
}
