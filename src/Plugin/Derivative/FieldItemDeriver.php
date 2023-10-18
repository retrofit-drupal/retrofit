<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class FieldItemDeriver extends DeriverBase implements ContainerDeriverInterface
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

    /**
     * @param array<string, mixed> $base_plugin_definition
     * @return array<string, array<string, string>>
     */
    public function getDerivativeDefinitions($base_plugin_definition)
    {
        $this->moduleHandler->invokeAllWith(
            'field_info',
            function (callable $hook, string $module) use ($base_plugin_definition) {
                $definitions = $hook();
                foreach ($definitions as $id => $definition) {
                    $derivative = $base_plugin_definition;
                    $derivative['label'] = $definition['label'] ?? '';
                    $derivative['description'] = $definition['description'] ?? '';
                    $derivative['default_widget'] = isset($definition['default_widget']) ?
                        "retrofit_field_widget:{$definition['default_widget']}" : '';
                    $derivative['default_formatter'] = isset($definition['default_formatter']) ?
                        "retrofit_field_formatter:{$definition['default_formatter']}" : '';
                    $derivative['no_ui'] = $definition['no_ui'] ?? false;
                    $derivative['cardinality'] = $definition['cardinality'] ?? null;
                    $derivative['provider'] = $module;
                    $derivative['field_info'] = $definition;
                    $this->derivatives[$id] = $derivative;
                }
            }
        );
        return $this->derivatives;
    }
}
