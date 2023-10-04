<?php

declare(strict_types=1);

function drupal_alter(string|array $type, mixed &$data, mixed &$context1 = null, mixed &$context2 = null, mixed &$context3 = null): void
{
    \Drupal::moduleHandler()->alter($type, $data, $context1, $context2);
}

function module_exists(string $module): bool
{
    return \Drupal::moduleHandler()->moduleExists($module);
}

/**
 * @return string[]
 */
function module_implements(string $hook, bool $sort = false, bool $reset = false): array
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
            $sorted[$hook] = $implementations;
            sort($sorted[$hook]);
        }
        return $sorted[$hook];
    }
    return $implementations;
}
