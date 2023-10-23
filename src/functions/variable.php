<?php

declare(strict_types=1);

use Drupal\Core\Routing\RouteBuilderInterface;

function variable_set(string $name, mixed $value): void
{
    if ($name === 'menu_rebuild_needed') {
        $route_builder = \Drupal::getContainer()->get('router.builder');
        assert($route_builder instanceof RouteBuilderInterface);
        $route_builder->setRebuildNeeded();
    } else {
        \Drupal::state()->set($name, $value);
    }
}

function variable_del(string $name): void
{
    \Drupal::state()->delete($name);
}

function variable_get(string $name, mixed $default = null): mixed
{
    return match ($name) {
        'clean_url' => true,
        'site_name' => \Drupal::config('system.site')->get('name') ?? $default,
        'site_slogan' => \Drupal::config('system.site')->get('slogan') ?? $default,
        default => \Drupal::state()->get($name, $default)
    };
}
