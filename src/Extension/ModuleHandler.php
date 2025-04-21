<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Extension;

use Drupal\Core\Extension\ModuleHandler as CoreModuleHandler;

final class ModuleHandler extends CoreModuleHandler
{
    /**
     * @return array<int, int|string>
     */
    public function getImplementations(string $hook): array
    {
        if (method_exists($this, 'getImplementationInfo')) {
            $implementations = $this->getImplementationInfo($hook);
            return array_keys($implementations);
        }

        return array_keys($this->invokeMap[$hook] ?? []);
    }
}
