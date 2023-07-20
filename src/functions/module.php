<?php

declare(strict_types=1);

function module_exists(string $module): bool
{
    return \Drupal::moduleHandler()->moduleExists($module);
}
