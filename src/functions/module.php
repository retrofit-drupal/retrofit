<?php

declare(strict_types=1);

function drupal_alter(string|array $type, &$data, &$context1 = null, &$context2 = null, &$context3 = null)
{
    \Drupal::moduleHandler()->alter($type, $data, $context1, $context2);
}

function module_exists(string $module): bool
{
    return \Drupal::moduleHandler()->moduleExists($module);
}

function module_implements(string $hook, ?bool $sort = false, ?bool $reset = false): array
{
    static $drupal_static_fast;
    if (!isset($drupal_static_fast)) {
        $drupal_static_fast['implementors'] = &drupal_static(__FUNCTION__, []);
        $drupal_static_fast['sorted'] = &drupal_static(__FUNCTION__ . ':sorted', []);
    }
    $implementors = &$drupal_static_fast['implementors'];
    if ($reset) {
        $implementors  = [];
        $sorted = [];
        \Drupal::moduleHandler()->resetImplementations();
    }
    if (!isset($implementors[$hook])) {
        $implementors[$hook] = [];
        \Drupal::moduleHandler()->invokeAllWith($hook, function (callable $callback, string $module) use (&$implementors, $hook) {
            $implementors[$hook][] = $module;
        });
    }
    if ($sort) {
        if (!isset($sorted[$hook])) {
            $sorted[$hook] = $implementors[$hook];
            sort($sorted[$hook]);
        }
        return $sorted[$hook];
    }
    return $implementors[$hook];
}
