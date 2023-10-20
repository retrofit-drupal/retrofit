<?php

declare(strict_types=1);

function user_access(string $string, ?AccountInterface $account = null): bool
{
    global $user;
    if (!isset($account)) {
        $account = $user;
    }
    return $account->hasPermission($string);
}

function user_is_anonymous(): bool
{
    return \Drupal::currentUser()->isAnonymous();
}

function user_is_logged_in(): bool
{
    return \Drupal::currentUser()->isAuthenticated();
}
