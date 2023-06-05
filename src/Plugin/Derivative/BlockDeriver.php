<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class BlockDeriver extends DeriverBase implements ContainerDeriverInterface
{
    public function __construct(
        private readonly ModuleHandlerInterface $moduleHandler
    ) {
    }

    public static function create(ContainerInterface $container, $base_plugin_id)
    {
        return new self(
            $container->get('module_handler')
        );
    }

    public function getDerivativeDefinitions($base_plugin_definition)
    {
        $this->moduleHandler->invokeAllWith(
            'block_info',
            function (callable $hook, string $module) use ($base_plugin_definition) {
                $definitions = $hook();
                foreach ($definitions as $block_id => $definition) {
                    $derivative = $base_plugin_definition;
                    $derivative['admin_label'] = $definition['info'];
                    $derivative['provider'] = $module;
                    $derivative['block_info'] = $definition;
                    $this->derivatives[$block_id] = $derivative;
                }
            }
        );
        return $this->derivatives;
    }
}
