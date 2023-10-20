<?php

declare(strict_types=1);

function lock_acquire(string $name, ?float $timeout = null): bool
{
    $lock = \Drupal::lock();
    return $lock->acquire($name, $timeout);
}
