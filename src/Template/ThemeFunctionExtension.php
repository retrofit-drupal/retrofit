<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Template;

use Drupal\Component\Render\MarkupInterface;
use Drupal\Core\Render\Markup;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ThemeFunctionExtension extends AbstractExtension
{
    /**
     * @return \Twig\TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
          new TwigFunction(
              'retrofit_theme_function',
              [$this, 'theme'],
              ['needs_context' => true]
          ),
        ];
    }

    /**
     * @param array<string, mixed> $context
     */
    public function theme(array $context): MarkupInterface|string
    {
        $themeFunction = 'theme_' . $context['theme_hook_original'];
        if (function_exists($themeFunction)) {
            return Markup::create($themeFunction($context));
        }
        return '';
    }
}
