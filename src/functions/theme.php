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
