<?php

declare(strict_types=1);

namespace Retrofit\Drupal\ParamConverter;

use Drupal\Core\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;

final class PageArgumentsConverter implements ParamConverterInterface
{
    /**
     * @param string $value
     * @param mixed[] $definition
     * @param string $name
     * @param array{_request: Request} $defaults
     */
    public function convert($value, $definition, $name, array $defaults): mixed
    {
        if (str_starts_with($name, 'arg')) {
            return $value;
        }
        if (function_exists($name . '_load') && is_callable($name . '_load')) {
            $map = explode('/', ltrim($defaults['_request']->getPathInfo(), '/'));
            foreach ((array) $definition['load arguments'] as &$arg) {
                $arg = match (true) {
                    is_numeric($arg) => $map[$arg],
                    $arg === 'map' => $map,
                    $arg === 'index' => $definition['index'],
                    default => $arg,
                };
            }
            $value = ($name . '_load')($value, ...$definition['load arguments']);
        }
        return $value;
    }

    public function applies($definition, $name, Route $route)
    {
        return $route->hasDefault('_menu_callback');
    }
}
