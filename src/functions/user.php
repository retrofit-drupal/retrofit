<?php

declare(strict_types=1);

use Drupal\Core\Session\AccountInterface;
use Drupal\user\RoleInterface;
use Retrofit\Drupal\Entity\Role;

function user_access(string $string, ?AccountInterface $account = null): bool
{
    if ($account === null) {
        $account = \Drupal::currentUser();
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

function user_role_load(string $rid): ?RoleInterface
{
    return Role::load($rid);
}

function user_role_load_by_name(string $role_name): RoleInterface|false
{
    return Role::load($role_name) ?? false;
}
