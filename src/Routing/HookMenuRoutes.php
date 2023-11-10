<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Retrofit\Drupal\ParamConverter\PageArgumentsConverter;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

final class HookMenuRoutes extends RouteSubscriberBase
{
    public function __construct(
        private readonly HookMenuRegistry $hookMenuRegistry
    ) {
    }

    protected function alterRoutes(RouteCollection $collection)
    {
        // @todo needs to process menu_alter
        foreach ($this->hookMenuRegistry->get() as $module => $routes) {
            foreach ($routes as $path => $definition) {
                // May be MENU_DEFAULT_LOCAL_TASK.
                $pageCallback = $definition['page callback'] ?? '';
                if ($pageCallback === '') {
                    continue;
                }
                $collection->add($definition['route_name'], $this->convertToRoute($module, $path, $definition));
            }
        }
    }

    private function convertToRoute(string $module, string $path, array $definition): Route
    {
        $pageArguments = $definition['page arguments'] ?? [];
        $parameters = [];
        $pathParts = [];
        foreach (explode('/', $path) as $key => $item) {
            if (!str_starts_with($item, '%')) {
                $pathParts[] = $item;
            } else {
                $placeholder = substr($item, 1);
                if ($placeholder === '') {
                    $placeholder = "arg$key";
                }
                $parameters[$placeholder] = [
                  'type' => $placeholder === '' ? $key : $placeholder,
                  'converter' => PageArgumentsConverter::class,
                  'load arguments' => $definition['load arguments'] ?? [],
                  'index' => $key,
                ];
                $pathParts[] = '{' . $placeholder . '}';
            }
        }
        $route = new Route('/' . implode('/', $pathParts));
        $route->setDefault('_title', $definition['title'] ?? '');

        $titleCallback = $definition['title callback'] ?? '';
        if ($titleCallback !== '') {
            $route->setDefault('_title_callback', '\Retrofit\Drupal\Controller\PageCallbackController::getTitle');
            $titleArguments = $definition['title arguments'] ?? [];
            $route->setDefault('_custom_title_callback', $titleCallback);
            $route->setDefault('_custom_title_arguments', $titleArguments);
        }

        $pageCallback = $definition['page callback'] ?? '';
        if ($pageCallback === 'drupal_get_form') {
            $route->setDefault('_controller', '\Retrofit\Drupal\Controller\DrupalGetFormController::getForm');
            $route->setDefault('_form_id', array_shift($pageArguments));
        } else {
            $route->setDefault('_controller', '\Retrofit\Drupal\Controller\PageCallbackController::getPage');
            $route->setDefault('_menu_callback', $pageCallback);
        }

        $accessCallback = $definition['access callback'] ?? '';
        $accessArguments = $definition['access arguments'] ?? [];
        if ($accessCallback === '' || $accessCallback === 'user_access') {
            $route->setRequirement('_permission', reset($accessArguments) ?: '');
        } elseif (is_bool($accessCallback)) {
            $route->setRequirement('_access', $accessCallback ? 'TRUE' : 'FALSE');
        } else {
            $route->setRequirement('_custom_access', '\Retrofit\Drupal\Access\CustomControllerAccessCallback::check');
            $route->setDefault('_custom_access_callback', $accessCallback);
            $route->setDefault('_custom_access_arguments', $accessArguments);
        }

        $route->setOption('module', $module);
        if (isset($definition['file'])) {
            $route->setOption('file', $definition['file']);
        }
        if (count($parameters) === 0 && count($pageArguments) > 0) {
            foreach ($pageArguments as $key => $pageArgument) {
                $parameters["arg$key"] = [
                  'type' => $pageArgument,
                  'converter' => PageArgumentsConverter::class,
                ];
                $route->setDefault("arg$key", $pageArgument);
            }
        }
        if (count($parameters) > 0) {
            $route->setOption('parameters', $parameters);
        }
        return $route;
    }
}
