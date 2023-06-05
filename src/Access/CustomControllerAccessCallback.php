<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Access\AccessResultInterface;
use Drupal\Core\Routing\RouteMatchInterface;

final class CustomControllerAccessCallback
{
    public function check(RouteMatchInterface $route_match): AccessResultInterface
    {
        $route = $route_match->getRouteObject();
        assert($route !== null);
        $access_callback = $route->getDefault('_custom_access_callback');
        $access_arguments = $route->getDefault('_custom_access_arguments');
        $result = call_user_func_array($access_callback, $access_arguments);

        return AccessResult::allowedIf(is_bool($result) && $result);
    }
}
