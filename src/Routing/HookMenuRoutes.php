<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Routing;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Routing\RouteSubscriberBase;
use Retrofit\Drupal\Access\CustomControllerAccessCallback;
use Retrofit\Drupal\Controller\DrupalGetFormController;
use Retrofit\Drupal\Controller\PageCallbackController;
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
        $pathParts = [];
        $parameters = [];
        $key = 0;
        foreach (explode('/', $path) as $item) {
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
            ++$key;
        }

        foreach ($loadArguments as &$loadArgument) {
            switch ($loadArgument) {
                case 'map':
                    $loadArgument = &$pathParts;
                    break;

                default:
                    $loadArgument = $this->ensureArgument($loadArgument, $pathParts, $parameters);
            }
        }
        $pageArguments = $definition['page arguments'];
        foreach ($pageArguments as &$pageArgument) {
            $pageArgument = $this->ensureArgument($pageArgument, $pathParts, $parameters);
        }
        $titleArguments = $definition['title arguments'];
        foreach ($titleArguments as &$titleArgument) {
            $titleArgument = $this->ensureArgument($titleArgument, $pathParts, $parameters);
        }
        $accessArguments = $definition['access arguments'];
        foreach ($accessArguments as &$accessArgument) {
            $accessArgument = $this->ensureArgument($accessArgument, $pathParts, $parameters);
        }

        $defaults = [];
        $pageCallback = match ($definition['page callback']) {
            'drupal_get_form' => array_shift($pageArguments),
            default => $definition['page callback'],
        };
        if (isset($definition['file'])) {
            $filePath = $definition['file path'] ?: $this->moduleHandler->getModule($module)->getPath();
            $definition['include file'] = $filePath . '/' . $definition['file'];
            if (file_exists($definition['include file'])) {
                require_once $definition['include file'];
            }
        }
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
                ) as $arg
            ) {
                $placeholder = "arg$key";
                if ($arg->isOptional()) {
                    $default = $arg->getDefaultValue();
                    switch (gettype($default)) {
                        case 'boolean':
                        case 'integer':
                        case 'double':
                        case 'string':
                        case 'NULL':
                            $defaults[$placeholder] = $default;
                            break;

                        case 'object':
                            if ($default instanceof \Stringable) {
                                $defaults[$placeholder] = $default;
                                break;
                            }
                            // No more placeholders.
                        default:
                            break 2;
                    }
                }
                $parameters[$placeholder] = ['converter' => PageArgumentsConverter::class];
                $pathParts[] = '{' . $placeholder . '}';
                $pageArguments[] = '{' . $placeholder . '}';
                ++$key;
            }
        }

        $route = new Route('/' . implode('/', $pathParts), $defaults);
        $route->setOption('module', $module);
        if (isset($definition['include file'])) {
            $route->setOption('include file', $definition['include file']);
        }
        if (count($parameters) > 0) {
            $route->setOption('parameters', $parameters);
        }

        if ($definition['page callback'] === 'drupal_get_form') {
            $route->setDefault('_controller', DrupalGetFormController::class . '::getForm');
            $route->setDefault('_form_id', $pageCallback);
        } else {
            $route->setDefault('_controller', PageCallbackController::class . '::getPage');
            $route->setDefault('_menu_callback', $definition['page callback']);
        }
        $route->setDefault('_custom_page_arguments', $pageArguments);

        $route->setDefault('_title', $definition['title']);
        if ($definition['title callback'] !== '') {
            $route->setDefault('_title_callback', PageCallbackController::class . '::getTitle');
            $route->setDefault('_custom_title_callback', $definition['title callback']);
            $route->setDefault('_custom_title_arguments', $titleArguments);
        }

        if ($definition['access callback'] === '' || $definition['access callback'] === 'user_access') {
            $route->setRequirement('_permission', (string) reset($accessArguments) ?: '');
        } elseif (is_bool($definition['access callback'])) {
            $route->setRequirement('_access', $definition['access callback'] ? 'TRUE' : 'FALSE');
        } else {
            $route->setRequirement('_custom_access', CustomControllerAccessCallback::class . '::check');
            $route->setDefault('_custom_access_callback', $definition['access callback']);
            $route->setDefault('_custom_access_arguments', $accessArguments);
        }

        return $route;
    }

    /**
     * @param array<int, string> $pathParts
     * @param array{
     *   converter: string,
     *   'load arguments'?: string[],
     *   index?: int
     * } $parameters
     */
    private function ensureArgument(mixed $argument, array &$pathParts, array &$parameters): mixed
    {
        if (is_int($argument)) {
            if ($argument >= count($pathParts)) {
                foreach (range(count($pathParts), $argument) as $key) {
                    $placeholder = "arg$key";
                    $parameters[$placeholder] = ['converter' => PageArgumentsConverter::class];
                    $pathParts[] = '{' . $placeholder . '}';
                }
            }
            $argument = $pathParts[$argument];
        }
        return $argument;
    }
}
