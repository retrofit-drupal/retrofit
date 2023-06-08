<?php

declare(strict_types=1);

use Drupal\Component\Render\MarkupInterface;

function check_plain(MarkupInterface|\Stringable|string $text): string
{
    return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}
