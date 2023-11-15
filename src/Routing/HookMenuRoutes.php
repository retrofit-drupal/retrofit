<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Routing;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Routing\RouteSubscriberBase;
use Retrofit\Drupal\ParamConverter\PageArgumentsConverter;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

final class HookMenuRoutes extends RouteSubscriberBase
{
    public function __construct(
        private readonly ModuleHandlerInterface $moduleHandler,
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

    /**
     * @param array{
     *   'page callback': string|string[],
     *   'page arguments'?: array<int|string>,
     *   'load arguments'?: array<int|string>,
     *   title?: string,
     *   'title callback'?: string|string[],
     *   'title arguments'?: array<int|string>,
     *   'access callback'?: string|string[]|bool,
     *   'access arguments'?: array<int|string>,
     *   file?: string,
     *   'file path'?: string
     * } $definition
     */
    private function convertToRoute(string $module, string $path, array $definition): Route
    {
        $definition += [
            'page arguments' => [],
            'load arguments' => [],
            'title' => '',
            'title callback' => '',
            'title arguments' => [],
            'access callback' => '',
            'access arguments' => [],
            'file path' => '',
        ];
        $loadArguments = $definition['load arguments'];
        $parameters = [];
        $pathParts = [];
        foreach (explode('/', $path) as $key => $item) {
            if (!str_starts_with($item, '%')) {
                $pathParts[] = $item;
            } else {
                $parameter = ['converter' => PageArgumentsConverter::class];
                $placeholder = substr($item, 1);
                if ($placeholder === '') {
                    $placeholder = "arg$key";
                } else {
                    $parameter += [
                        'load arguments' => &$loadArguments,
                        'index' => $key,
                    ];
                }
                $parameters[$placeholder] = $parameter;
                $pathParts[] = '{' . $placeholder . '}';
            }
        }
        foreach ($loadArguments as &$loadArgument) {
            $loadArgument = match (true) {
                is_int($loadArgument) => $pathParts[$loadArgument],
                $loadArgument === 'map' => $pathParts,
                default => $loadArgument,
            };
        }
        $pageArguments = $definition['page arguments'];
        foreach ($pageArguments as &$pageArgument) {
            if (is_int($pageArgument)) {
                $pageArgument = $pathParts[$pageArgument];
            }
        }
        if (isset($definition['file'])) {
            $filePath = $definition['file path'] ?: $this->moduleHandler->getModule($module)->getPath();
            $definition['include file'] = $filePath . '/' . $definition['file'];
            if (file_exists($definition['include file'])) {
                require_once $definition['include file'];
            }
        }
        $defaults = [];
        $pageCallback = match ($definition['page callback']) {
            'drupal_get_form' => array_shift($pageArguments),
            default => $definition['page callback'],
        };
        if (is_callable($pageCallback)) {
            $skip = $definition['page callback'] === 'drupal_get_form' ? 2 : 0;
            $reflectedPageCallback = match (true) {
                is_array($pageCallback) => new \ReflectionMethod(...$pageCallback),
                strpos($pageCallback, '::') !== false => new \ReflectionMethod(
                    ...explode('::', $pageCallback, 2)
                ),
                default => new \ReflectionFunction($pageCallback),
            };
            foreach (
                array_slice(
                    $reflectedPageCallback->getParameters(),
                    count($pageArguments) + $skip,
                    null,
                    true
                ) as $key => $arg
            ) {
                $placeholder = "arg$key";
                $parameters[$placeholder] = [
                    'converter' => PageArgumentsConverter::class,
                ];
                $pathParts[] = '{' . $placeholder . '}';
                $pageArguments[] = '{' . $placeholder . '}';
                if ($arg->isOptional()) {
                    $defaults[$placeholder] = $arg->getDefaultValue();
                }
            }
        }
        $route = new Route('/' . implode('/', $pathParts), $defaults);
        $route->setDefault('_title', $definition['title']);

        if ($definition['title callback'] !== '') {
            $route->setDefault('_title_callback', '\Retrofit\Drupal\Controller\PageCallbackController::getTitle');
            $titleArguments = $definition['title arguments'];
            foreach ($titleArguments as &$titleArgument) {
                if (is_int($titleArgument)) {
                    $titleArgument = $pathParts[$titleArgument];
                }
            }
            $route->setDefault('_custom_title_callback', $definition['title callback']);
            $route->setDefault('_custom_title_arguments', $titleArguments);
        }

        if ($definition['page callback'] === 'drupal_get_form') {
            $route->setDefault('_controller', '\Retrofit\Drupal\Controller\DrupalGetFormController::getForm');
            $route->setDefault('_form_id', $pageCallback);
        } else {
            $route->setDefault('_controller', '\Retrofit\Drupal\Controller\PageCallbackController::getPage');
            $route->setDefault('_menu_callback', $definition['page callback']);
        }

        $accessArguments = $definition['access arguments'];
        if ($definition['access callback'] === '' || $definition['access callback'] === 'user_access') {
            $route->setRequirement('_permission', (string) reset($accessArguments) ?: '');
        } elseif (is_bool($definition['access callback'])) {
            $route->setRequirement('_access', $definition['access callback'] ? 'TRUE' : 'FALSE');
        } else {
            $route->setRequirement('_custom_access', '\Retrofit\Drupal\Access\CustomControllerAccessCallback::check');
            $route->setDefault('_custom_access_callback', $definition['access callback']);
            foreach ($accessArguments as &$accessArgument) {
                if (is_int($accessArgument)) {
                    $accessArgument = $pathParts[$accessArgument];
                }
            }
            $route->setDefault('_custom_access_arguments', $accessArguments);
        }

        $route->setOption('module', $module);
        if (isset($definition['include file'])) {
            $route->setOption('include file', $definition['include file']);
        }
        $route->setDefault('_custom_page_arguments', $pageArguments);
        if (count($parameters) > 0) {
            $route->setOption('parameters', $parameters);
        }
        return $route;
    }
}
