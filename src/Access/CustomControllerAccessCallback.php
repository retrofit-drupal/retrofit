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
        if (!is_callable($access_callback)) {
            return AccessResult::forbidden(sprintf(
                'Access callback "%s" is not callable for "%s".',
                is_scalar($access_callback) ? $access_callback : gettype($access_callback),
                $route_match->getRouteName()
            ));
        }
        $access_arguments = $route->getDefault('_custom_access_arguments') ?? [];
        if (!is_array($access_arguments)) {
            return AccessResult::forbidden(sprintf(
                'Access arguments is not array for "%s".',
                $route_match->getRouteName()
            ));
        }
        foreach ($access_arguments as &$argument) {
            if (is_string($argument) && $placeholder = preg_filter('/(^{)(.*)(}$)/', '$2', $argument)) {
                $argument = $route_match->getParameter($placeholder);
            }
        }
        /** @var array<int|string, mixed> $access_arguments */
        $result = call_user_func_array($access_callback, $access_arguments);
        return AccessResult::allowedIf(is_bool($result) && $result);
    }
}
