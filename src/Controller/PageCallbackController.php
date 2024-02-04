<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Controller;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Render\Markup;
use Drupal\Core\Routing\RouteMatchInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class PageCallbackController implements ContainerInjectionInterface
{
    public static function create(ContainerInterface $container)
    {
        return new self();
    }

    public function getTitle(RouteMatchInterface $routeMatch): string
    {
        $route = $routeMatch->getRouteObject();
        assert($route !== null);
        $callback = $route->getDefault('_custom_title_callback');
        if (!is_callable($callback)) {
            return '';
        }
        $arguments = (array) $route->getDefault('_custom_title_arguments');
        foreach ($arguments as &$argument) {
            if (is_string($argument) && $placeholder = preg_filter('/(^{)(.*)(}$)/', '$2', $argument)) {
                $argument = $routeMatch->getParameter($placeholder);
            }
        }
        return call_user_func_array($callback, $arguments);
    }

    public function getPage(RouteMatchInterface $routeMatch, Request $request): array
    {
        $route = $routeMatch->getRouteObject();
        assert($route !== null);
        if ($route->hasOption('include file')) {
            $includePath = $route->getOption('include file');
            assert(is_string($includePath));
            if (file_exists($includePath)) {
                require_once $includePath;
            }
        }
        $callback = $route->getDefault('_menu_callback');
        if (!is_callable($callback)) {
            throw new NotFoundHttpException();
        }
        $arguments = (array) $route->getDefault('_custom_page_arguments');
        foreach ($arguments as &$argument) {
            if (is_string($argument) && $placeholder = preg_filter('/(^{)(.*)(}$)/', '$2', $argument)) {
                $argument = $routeMatch->getParameter($placeholder);
            }
        }
        $result = call_user_func_array($callback, array_values($arguments));
        return is_string($result) ? [
          '#markup' => Markup::create($result),
        ] : $result;
    }
}
