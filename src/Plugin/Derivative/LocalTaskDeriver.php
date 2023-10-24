<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\Core\Routing\RouteProviderInterface;
use Retrofit\Drupal\Routing\HookMenuRegistry;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LocalTaskDeriver extends DeriverBase implements ContainerDeriverInterface
{
    public function __construct(
        private readonly HookMenuRegistry $hookMenuRegistry,
        private readonly RouteProviderInterface $routeProvider
    ) {
    }

    public static function create(
        ContainerInterface $container,
        $base_plugin_id
    ) {
        return new self(
            $container->get(HookMenuRegistry::class),
            $container->get('router.route_provider')
        );
    }

    public function getDerivativeDefinitions($base_plugin_definition)
    {
        foreach ($this->hookMenuRegistry->get() as $module => $routes) {
            foreach ($routes as $path => $definition) {
                if (
                    !in_array($definition['type'], [
                        MENU_LOCAL_TASK,
                        MENU_DEFAULT_LOCAL_TASK,
                    ])
                ) {
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
                while (
                    ($pos = strrpos($path, '/'))
                    && $path = substr($path, 0, $pos)
                ) {
                    if ($parent = key($this->routeProvider->getRoutesByPattern($path)->all())) {
                        break;
                    }
                }
                switch ($definition['type']) {
                    case MENU_DEFAULT_LOCAL_TASK:
                        if ($parent) {
                            $menuLinkDefinition['route_name'] = $parent;
                        }
                        // In other ways this is a normal local task.
                    case MENU_LOCAL_TASK:
                        if ($parent) {
                            $menuLinkDefinition['base_route'] = $parent;
                        } else {
                            $menuLinkDefinition['base_route'] = $definition['route_name'];
                        }
                        break;
                }
                $this->derivatives[$definition['route_name']] = $menuLinkDefinition;
            }
        }
        return $this->derivatives;
    }
}
