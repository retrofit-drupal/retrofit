<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Template;

use Drupal\Core\Render\Markup;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ThemeFunctionExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
          new TwigFunction(
              'retrofit_theme_function',
              [$this, 'theme'],
              [
              'needs_context' => true,
              'needs_environment' => true,
              'is_variadic' => true,
              ]
          ),
        ];
    }

    public function theme($env, $context, ...$variables)
    {
        $themeFunction = 'theme_' . $context['theme_hook_original'];
        if (function_exists($themeFunction)) {
            return Markup::create($themeFunction($context));
        }
        return '';
    }
}
