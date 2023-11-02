<?php

declare(strict_types=1);

function lock_acquire(string $name, float $timeout = 30.0): bool
{
    return \Drupal::lock()->acquire($name, $timeout);
}

function lock_may_be_available(string $name): bool
{
    return \Drupal::lock()->lockMayBeAvailable($name);
}

function lock_wait(string $name, int $delay = 30): bool
{
    return \Drupal::lock()->wait($name, $delay);
}

function lock_release(string $name): void
{
    \Drupal::lock()->release($name);
}

function lock_release_all(?string $lock_id = null): void
{
    \Drupal::lock()->releaseAll($lock_id);
}
