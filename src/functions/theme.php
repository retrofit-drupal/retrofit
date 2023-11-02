<?php

declare(strict_types=1);

use Drupal\Component\Render\MarkupInterface;
use Drupal\Core\Render\RendererInterface;

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
