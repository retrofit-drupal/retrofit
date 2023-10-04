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
    $module_handler = \Drupal::moduleHandler();
    if ($reset) {
        $module_handler->resetImplementations();
        $sorted = [];
    }
    $implementations = $module_handler->getImplementations($hook);
    if ($sort) {
        $sorted = &drupal_static(__FUNCTION__, []);
        if (!isset($sorted[$hook])) {
            $sorted[$hook] = $implementations[$hook];
            sort($sorted[$hook]);
        }
        return $sorted[$hook];
    }
    return $implementations[$hook];
}
