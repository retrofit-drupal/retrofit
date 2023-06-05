<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Tests\Integration;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\KernelTests\KernelTestBase;
use Retrofit\Drupal\Tests\Utils\TestModuleHandler;
use Symfony\Component\DependencyInjection\Reference;

abstract class IntegrationTestCase extends KernelTestBase
{
    protected static function getTestModules(): array
    {
        return [];
    }

    public function register(ContainerBuilder $container)
    {
        parent::register($container);
        $modules = $container->getParameter('container.modules');
        foreach (static::getTestModules() as $module) {
            $modules[$module] = [
              'type' => 'module',
              'pathname' => "../../tests/data/$module/$module.module",
              'filename' => "$module.module",
            ];
        }
        $container->setParameter('container.modules', $modules);

        $container->register('test_module_handler', TestModuleHandler::class)
          ->setDecoratedService('module_handler')
          ->addArgument(new Reference('test_module_handler.inner'));
    }
}
