<?php

declare(strict_types=1);

use Drupal\Core\Extension\ExtensionList;

/**
 * @return mixed[]
 */
function system_get_info(string $type, ?string $name = null): array
{
    $service = \Drupal::service("extension.list.$type");
    assert($service instanceof ExtensionList);
    $info = $service->getAllInstalledInfo();
    if (isset($name)) {
        return $info[$name] ?? [];
    }
    return $info;
}
