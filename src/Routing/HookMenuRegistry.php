<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Routing;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;

final class HookMenuRegistry
{
    private array $definitions;

    public function __construct(
        private readonly ModuleHandlerInterface $moduleHandler,
        private readonly CacheBackendInterface $cacheBackend,
    ) {
    }

    public function get(): array
    {
        if (isset($this->definitions)) {
            return $this->definitions;
        }
        $cache = $this->cacheBackend->get('retrofit.hook_menu_registry');
        if ($cache !== false) {
            $this->definitions = $cache->data;
            return $this->definitions;
        }
        $this->definitions = [];
        $this->moduleHandler->invokeAllWith('menu', function (callable $hook, string $module) {
            $definitions = $hook();
            foreach ($definitions as $path => $definition) {
                  $definition['type'] = $definition['type'] ?? MENU_NORMAL_ITEM;
                  $definition['route_name'] = $module . '.' . substr(hash('sha256', $path), 0, 25);
                  $definitions[$path] = $definition;
            }
            $this->definitions[$module] = $definitions;
        });
        $this->cacheBackend->set('retrofit.hook_menu_registry', $this->definitions);
        return $this->definitions;
    }
}
