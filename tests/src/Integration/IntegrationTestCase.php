<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Tests\Integration;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\KernelTests\KernelTestBase;
use Retrofit\Drupal\Tests\Utils\TestExtensionInstallStorage;
use Retrofit\Drupal\Tests\Utils\TestModuleHandler;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Reference;

abstract class IntegrationTestCase extends KernelTestBase
{
    /**
     * @return string[]
     */
    protected static function getTestModules(): array
    {
        return [];
    }

    public function register(ContainerBuilder $container): void
    {
        parent::register($container);
        /** @var array<string, array{type: string, pathname: string, filename: string}> $modules */
        $modules = $container->getParameter('container.modules');
        foreach (static::getTestModules() as $module) {
            $modules[$module] = [
              'type' => 'module',
              'pathname' => "../../tests/data/$module/$module.info.yml",
              'filename' => "$module.module",
            ];
        }
        $container->setParameter('container.modules', $modules);

        $container->register('test_module_handler', TestModuleHandler::class)
          ->setDecoratedService('module_handler')
          ->addArgument(new Reference('test_module_handler.inner'));

        $container->setDefinition(
            TestExtensionInstallStorage::class,
            (new ChildDefinition('config.storage.schema'))
                ->setDecoratedService('config.storage.schema')
        );
    }
}
