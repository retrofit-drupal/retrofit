<?php

declare(strict_types=1);

use Retrofit\Drupal\Provider;

$GLOBALS['conf']['container_service_providers']['retrofit'] = Provider::class;


spl_autoload_register(function (string $item) {
    if (!\Drupal::hasContainer()) {
        return null;
    }
    static $files;
    if ($files === null) {
        if (!\Drupal::getContainer()->hasParameter('files_autoload_registry')) {
            $files = [];
        } else {
            $files = \Drupal::getContainer()->getParameter('files_autoload_registry');
        }
    }
    if (isset($files[$item])) {
        include $files[$item];
        return true;
    }
    return null;
});
