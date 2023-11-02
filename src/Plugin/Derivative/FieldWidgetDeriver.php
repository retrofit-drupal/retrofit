<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FieldWidgetDeriver extends DeriverBase implements ContainerDeriverInterface
{
    public function __construct(
        private readonly ModuleHandlerInterface $moduleHandler
    ) {
    }

    public static function create(ContainerInterface $container, $base_plugin_id): self
    {
        return new self(
            $container->get('module_handler')
        );
    }

    /**
     * @param mixed[] $base_plugin_definition
     * @return mixed[]
     */
    public function getDerivativeDefinitions($base_plugin_definition): array
    {
        $this->moduleHandler->invokeAllWith(
            'field_widget_info',
            function (callable $hook, string $module) use ($base_plugin_definition) {
                $definitions = $hook();
                foreach ($definitions as $id => $definition) {
                    $derivative = $base_plugin_definition;
                    $derivative['label'] = $definition['label'] ?? '';
                    $derivative['description'] = $definition['description'] ?? '';
                    $derivative['field types'] = $definition['field types'] ?? [];
                    $derivative['multiple_values'] = isset($definition['behaviors']['multiple values'])
                        && $definition['behaviors']['multiple values'] == FIELD_BEHAVIOR_CUSTOM;
                    $derivative['weight'] = $definition['weight'] ?? null;
                    $derivative['provider'] = $module;
                    $derivative['widget'] = $id;
                    $derivative['field_widget_info'] = $definition;
                    $this->derivatives[$id] = $derivative;
                }
            }
        );
        return $this->derivatives;
    }
}
