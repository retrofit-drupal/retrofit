<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Menu;

use Retrofit\Drupal\Routing\HookMenuRegistry;

final class MenuLinkManager extends \Drupal\Core\Menu\MenuLinkManager
{
    private readonly HookMenuRegistry $hookMenuRegistry;

    public function setHookMenuRegistry(HookMenuRegistry $hookMenuRegistry): void
    {
        $this->hookMenuRegistry = $hookMenuRegistry;
    }

    public function getDefinitions()
    {
        // @todo find a way to improve this – maybe a custom discovery that
        //   decorates the YAML one and has the hook menu registry?
        $definitions = parent::getDefinitions();
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
                // @todo should we automatically map `main-menu` to main`?
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
                $this->processDefinition(
                    $menuLinkDefinition,
                    $definition['route_name']
                );
                $definitions[$definition['route_name']] = $menuLinkDefinition;
            }
        }

        return $definitions;
    }
}
