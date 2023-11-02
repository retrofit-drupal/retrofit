<?php

declare(strict_types=1);

/**
 * @param string|string[] $type
 */
function drupal_alter(
    string|array $type,
    mixed &$data,
    mixed &$context1 = null,
    mixed &$context2 = null,
    mixed &$context3 = null
): void {
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

function module_invoke(string $module, string $hook): mixed
{
    $args = func_get_args();
    unset($args[0], $args[1]);
    return \Drupal::moduleHandler()->invoke($module, $hook, $args);
}

/**
 * @return array<int, mixed>
 */
function module_invoke_all(string $hook): array
{
    $args = func_get_args();
    unset($args[0]);
    return \Drupal::moduleHandler()->invokeAll($hook, $args);
}

/**
 * @param ?string[] $fixed_list
 * @return string[]
 */
function module_list(
    bool $refresh = false,
    bool $bootstrap_refresh = false,
    bool $sort = false,
    ?array $fixed_list = null
): array {
    static $list = [], $sorted_list;
    if ($refresh || $fixed_list) {
        $list = [];
        $sorted_list = null;
        if ($fixed_list !== null) {
            foreach (array_keys($fixed_list) as $name) {
                drupal_get_filename('module', $name);
                $list[$name] = $name;
            }
        } elseif ($refresh || $bootstrap_refresh) {
            // These do nothing, now.
        } else {
            $list = array_keys(\Drupal::moduleHandler()->getModuleList());
            $list = array_combine($list, $list);
        }
    }
    if ($sort) {
        if (!isset($sorted_list)) {
            $sorted_list = $list;
            ksort($sorted_list);
        }
        return $sorted_list;
    }
    return $list;
}
