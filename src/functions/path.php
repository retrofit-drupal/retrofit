<?php

declare(strict_types=1);

use Drupal\Core\Path\CurrentPathStack;

function current_path(): string
{
    $service = \Drupal::service('path.current');
    return $service instanceof CurrentPathStack ? $service->getPath() : '';
}

function path_is_admin(): bool
{
    return \Drupal::service('router.admin_context')->isAdminRoute();
}
