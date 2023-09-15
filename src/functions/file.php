<?php

declare(strict_types=1);

function file_scan_directory(string $dir, string $mask, ?array $options = []): array
{
    $files = [];
    if (is_dir($dir)) {
        $files = \Drupal::service('file_system')->scanDirectory($dir, $mask, $options);
    }
    return $files;
}
