<?php

declare(strict_types=1);

use Drupal\Core\Path\CurrentPathStack;
use Retrofit\Drupal\Routing\HookAdminPaths;

function current_path(): string
{
    $service = \Drupal::service('path.current');
    return $service instanceof CurrentPathStack ? $service->getPath() : '';
}

function path_is_admin(): bool
{
    return \Drupal::service('router.admin_context')->isAdminRoute();
}

/**
 * @return array{admin: string, non_admin: string}
 */
function path_get_admin_paths(): array
{
    /** @var array{admin: string, non_admin: string}|null $patterns */
    $patterns = &drupal_static(__FUNCTION__);
    if ($patterns === null) {
        $hookAdminPaths = \Drupal::getContainer()->get(HookAdminPaths::class);
        assert($hookAdminPaths instanceof HookAdminPaths);
        $patterns = $hookAdminPaths->get();
    }
    return $patterns;
}
