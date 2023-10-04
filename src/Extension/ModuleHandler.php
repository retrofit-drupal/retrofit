<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Extension;

final class ModuleHandler extends Drupal\Core\Extension\ModuleHandler
{
    public function getImplementations(string $hook): array
    {
        $implementations = $this->getImplementationInfo($hook);
        return array_keys($implementations);
    }
}
