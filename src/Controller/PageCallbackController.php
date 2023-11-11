<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Controller;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Render\Markup;
use Drupal\Core\Routing\RouteMatchInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class PageCallbackController implements ContainerInjectionInterface
{
    public function __construct(
        private readonly ModuleHandlerInterface $moduleHandler
    ) {
    }

    public static function create(ContainerInterface $container): self
    {
        return new self(
            $container->get('module_handler')
        );
    }

    public function getTitle(RouteMatchInterface $routeMatch): string|\Stringable
    {
        $route = $routeMatch->getRouteObject();
        assert($route !== null);
        $callback = $route->getDefault('_custom_title_callback');
        if (!is_callable($callback)) {
            return '';
        }
        $arguments = $route->getDefault('_custom_title_arguments');
        if (!is_array($arguments)) {
                throw new \InvalidArgumentException(
                    'The "_custom_title_arguments" default must be a string'
                );
        }
        /** @var array<int|string, mixed> $arguments */
        $title = call_user_func_array($callback, $arguments);
        if (!is_string($title) && !$title instanceof \Stringable) {
            throw new \InvalidArgumentException(
                'The "title" result must be a string or \Stringable'
            );
        }
        return $title;
    }

    /**
     * @return array<string, mixed>
     */
    public function getPage(RouteMatchInterface $routeMatch): array
    {
        $route = $routeMatch->getRouteObject();
        assert($route !== null);
        if ($route->hasOption('file')) {
            $module = $route->getOption('module');
            if (!is_string($module)) {
                throw new \RuntimeException('Module name must be a string.');
            }
            $modulePath = $this->moduleHandler->getModule($module)->getPath();
            $includePath = $modulePath . '/' . $route->getOption('file');
            if (file_exists($includePath)) {
                require_once $includePath;
            }
        }
        $callback = $route->getDefault('_menu_callback');
        if (!is_callable($callback)) {
            throw new NotFoundHttpException();
        }
        $arguments = $routeMatch->getParameters()->all();
        $result = call_user_func_array($callback, array_values($arguments));
        return is_string($result) ? [
          '#markup' => Markup::create($result),
        ] : $result;
    }
}
