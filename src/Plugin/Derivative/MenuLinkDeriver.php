<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Retrofit\Drupal\Routing\HookMenuRegistry;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class MenuLinkDeriver extends DeriverBase implements ContainerDeriverInterface
{
    public function __construct(
        private readonly HookMenuRegistry $hookMenuRegistry
    ) {
    }

    public static function create(
        ContainerInterface $container,
        $base_plugin_id
    ) {
        return new self(
            $container->get(HookMenuRegistry::class),
        );
    }

    public function getDerivativeDefinitions($base_plugin_definition)
    {
        foreach ($this->hookMenuRegistry->get() as $module => $routes) {
            foreach ($routes as $definition) {
                if ($definition['type'] !== MENU_NORMAL_ITEM) {
                    continue;
                }
                $menuLinkDefinition = [
                  'id' => $definition['route_name'],
                  'title' => $definition['title'] ?? '',
                  'description' => $definition['description'] ?? '',
                  'route_name' => $definition['route_name'],
                  'expanded' => $definition['expanded'] ?? false,
                  'weight' => $definition['weight'] ?? 0,
                  'provider' => $module,
                ];
                $menuName = $definition['menu_name'] ?? '';
                if ($menuName !== '') {
                    $menuMapping = [
                      'main-menu' => 'main',
                      'management' => 'admin',
                      'navigation' => 'tools',
                      'user-menu' => 'account',
                    ];
                    $menuLinkDefinition['menu_name'] = $menuMapping[$menuName] ?? $menuName;
                }
                $this->derivatives[$definition['route_name']] = $menuLinkDefinition;
            }
        }
        return $this->derivatives;
    }
}
