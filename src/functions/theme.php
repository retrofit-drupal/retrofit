<?php

declare(strict_types=1);

use Drupal\Component\Render\MarkupInterface;
use Drupal\Core\Extension\ThemeHandlerInterface;
use Drupal\Core\Render\RendererInterface;
use Retrofit\Drupal\Extension\ExtensionWrapper;

/**
 * @return array<string, ExtensionWrapper>
 */
function list_themes(bool $refresh = false): array
{
    $list = [];
    $service = \Drupal::service('theme_handler');
    if ($service instanceof ThemeHandlerInterface) {
        $list = array_map(ExtensionWrapper::class . '::create', $service->listInfo());
    };
    return $list;
}

/**
 * @param array<string, mixed> $variables
 */
function theme(string $hook, array $variables = []): MarkupInterface|string
{
    $build['#theme'] = $hook;
    if (function_exists('retrofit_preprocess_' . $hook)) {
        ('retrofit_preprocess_' . $hook)($variables);
    }
    foreach ($variables as $key => $variable) {
        $build["#$key"] = $variable;
    }
    $renderer = \Drupal::service('renderer');
    assert($renderer instanceof RendererInterface);
    return $renderer->render($build);
}

function path_to_theme(): string
{
    return \Drupal::theme()->getActiveTheme()->getPath();
}

/**
 * @param array{
*      links?: array{title: string, html?: bool, href?: string, attributes?: array{class?: string[]}},
*      heading?: mixed[]
 * } $variables
 */
function retrofit_preprocess_links(array &$variables): void
{
    if (!empty($variables['links'])) {
        foreach ($variables['links'] as $key => &$link) {
            $link += ['attributes' => []];
            $link['attributes']['class'][] = $key;
            if (isset($link['html']) && !empty($link['html'])) {
                $link['title'] = ['#markup' => $link['title']];
                unset($link['html']);
            }
            if (
                isset($link['href'])
                && ($url = \Drupal::pathValidator()->getUrlIfValidWithoutAccessCheck($link['href']))
            ) {
                $url->mergeOptions($link);
                $link['url'] = $url;
                unset($link['href']);
            }
        }
    }
    if (!empty($variables['heading'])) {
        if (!empty($variables['heading']['class'])) {
            $variables['heading']['attributes']['class'] = $variables['heading']['class'];
            unset($variables['heading']['class']);
        }
        $links['#heading'] = $variables['heading'];
    }
}
