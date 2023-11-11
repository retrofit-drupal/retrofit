<?php

declare(strict_types=1);

use Drupal\Core\Extension\ModuleExtensionList;

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
 * @param array<string, array{filename: string}> $fixed_list
 * @return string[]
 */
function module_list(
    bool $refresh = false,
    bool $bootstrap_refresh = false,
    bool $sort = false,
    ?array $fixed_list = null
): array {
    if ($fixed_list !== null) {
        $moduleExtensionList = \Drupal::service('extension.list.module');
        $moduleNames = array_keys($fixed_list);
        $newList = array_map(
            static fn (string $name) => $moduleExtensionList->get($name),
            $moduleNames
        );
        \Drupal::moduleHandler()->setModuleList(
            array_combine($moduleNames, $newList)
        );
    }
    $list = array_keys(\Drupal::moduleHandler()->getModuleList());
    $list = array_combine($list, $list);

    if ($sort) {
        ksort($list);
    }
    return $list;
}
