<?php

declare(strict_types=1);

function lock_acquire(string $name, ?float $timeout = null): bool
{
    return \Drupal::lock()->acquire($name, $timeout);
}

function lock_release(string $name): void
{
    \Drupal::lock()->release($name);
}
