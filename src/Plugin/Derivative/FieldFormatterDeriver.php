<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class FieldFormatterDeriver extends DeriverBase implements ContainerDeriverInterface
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
            'field_formatter_info',
            function (callable $hook, string $module) use ($base_plugin_definition) {
                $definitions = $hook();
                foreach ($definitions as $id => $definition) {
                    $derivative = $base_plugin_definition;
                    $derivative['provider'] = $module;
                    $derivative['field_formatter_info'] = $definition;
                    $derivative['label'] = $definition['label'];
                    $derivative['field_types'] = array_map(
                        static fn (string $type) => "retrofit_field:$type",
                        $definition['field types'] ?? []
                    );
                    $this->derivatives[$id] = $derivative;
                }
            }
        );
        return $this->derivatives;
    }
}
