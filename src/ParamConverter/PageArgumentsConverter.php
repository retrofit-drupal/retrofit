<?php

declare(strict_types=1);

namespace Retrofit\Drupal\ParamConverter;

use Drupal\Core\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\Routing\Route;

final class PageArgumentsConverter implements ParamConverterInterface
{
    /**
     * @param string $value
     * @param mixed[] $definition
     * @param string $name
     * @param array{_raw_variables: InputBag} $defaults
     */
    public function convert($value, $definition, $name, array $defaults): mixed
    {
        if (str_starts_with($name, 'arg')) {
            return $value;
        }
        if (function_exists($name . '_load') && is_callable($name . '_load')) {
            foreach ((array) $definition['load arguments'] as &$argument) {
                if (is_array($argument)) {
                    foreach ($argument as &$arg) {
                        if (is_string($arg) && $placeholder = preg_filter('/(^{)(.*)(}$)/', '$2', $arg)) {
                            $arg = $defaults['_raw_variables']->get($placeholder);
                        }
                    }
                } elseif ($argument === 'index') {
                    $argument = $definition['index'];
                } elseif (is_string($argument) && $placeholder = preg_filter('/(^{)(.*)(}$)/', '$2', $argument)) {
                    $argument = $defaults['_raw_variables']->get($placeholder);
                }
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
