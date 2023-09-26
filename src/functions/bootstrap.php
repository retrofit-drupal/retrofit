<?php

declare(strict_types=1);

use Drupal\Component\Render\MarkupInterface;
use Drupal\Core\Extension\ExtensionPathResolver;

function check_plain(MarkupInterface|\Stringable|string $text): string
{
    return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

function drupal_get_filename(string $type, string $name, ?string $filename = null, bool $trigger_error = false): ?string
{
    $pathResolver = \Drupal::service('extension.path.resolver');
    assert($pathResolver instanceof ExtensionPathResolver);
    return $pathResolver->getPathname($type, $name);
}

function get_t(): string
{
    return 't';
}
