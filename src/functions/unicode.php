<?php

declare(strict_types=1);

function drupal_strtolower(string $text): string
{
    return mb_strtolower($text);
}
