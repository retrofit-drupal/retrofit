<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Template;

use Drupal\Component\Render\MarkupInterface;
use Drupal\Core\Render\Markup;
use Drupal\Core\Theme\Registry;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class RetrofitExtension extends AbstractExtension
{
    public function __construct(
        private readonly Registry $themeRegistry,
    ) {
    }

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
          new TwigFunction(
              'retrofit_theme_phptemplate',
              [$this, 'phptemplate'],
              ['needs_context' => true]
          ),
        ];
    }

    /**
     * @param array{theme_hook_original: string} $context
     */
    public function theme(array $context): MarkupInterface|string
    {
        $themeFunction = 'theme_' . $context['theme_hook_original'];
        if (function_exists($themeFunction)) {
            return Markup::create($themeFunction($context));
        }
        return '';
    }

    /**
     * @param array{theme_hook_original: string} $context
     */
    public function phptemplate(array $context): MarkupInterface|string
    {
        $theme_hook = $context['theme_hook_original'];
        /** @var array{path: string, template: string, phptemplate?: string} $info */
        $info = $this->themeRegistry->getRuntime()->get($theme_hook);
        $template_file = $info['phptemplate'] ?? '';
        if (!file_exists($template_file)) {
            return '';
        }
        extract($context, EXTR_SKIP);
        ob_start();
        include $template_file;
        return Markup::create(ob_get_clean() ?: '');
    }
}
