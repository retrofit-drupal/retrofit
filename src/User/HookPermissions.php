<?php

declare(strict_types=1);

namespace Retrofit\Drupal\User;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\user\PermissionHandlerInterface;

final class HookPermissions implements PermissionHandlerInterface
{
    public function __construct(
        private readonly PermissionHandlerInterface $inner,
        private readonly ModuleHandlerInterface $moduleHandler
    ) {
    }

    public function getPermissions()
    {
        $all_permissions = $this->inner->getPermissions();
        $this->moduleHandler->invokeAllWith(
            'permission',
            function (callable $hook, string $module) use (&$all_permissions) {
                $permissions = $hook();
                foreach ($permissions as $permission => $data) {
                    $permissions[$permission]['description'] = $data['description'] ?? '';
                    $permissions[$permission]['provider'] = $module;
                }
                $all_permissions += $permissions;
            }
        );
        return $all_permissions;
    }

    public function moduleProvidesPermissions($module_name)
    {
        return $this->inner->moduleProvidesPermissions($module_name);
    }
}
