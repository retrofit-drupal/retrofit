<?php

declare(strict_types=1);

use Drupal\Core\File\FileSystemInterface;

function drupal_realpath(string $uri): string|false
{
    $service = \Drupal::service('file_system');
    return !empty($uri) && $service instanceof FileSystemInterface ? $service->realpath($uri) : false;
}

function file_prepare_directory(string &$directory, ?int $options = FileSystemInterface::MODIFY_PERMISSIONS): bool
{
    return \Drupal::service('file_system')->prepareDirectory($directory, $options);
}

/**
 * @param mixed[] $options
 *
 * @return object[]
 */
function file_scan_directory(string $dir, string $mask, array $options = []): array
{
    $files = [];
    if (is_dir($dir)) {
        $files = \Drupal::service('file_system')->scanDirectory($dir, $mask, $options);
    }
    return $files;
}
