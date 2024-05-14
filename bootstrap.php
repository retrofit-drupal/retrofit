<?php

declare(strict_types=1);

use Retrofit\Drupal\Provider;

$GLOBALS['conf']['container_service_providers']['retrofit'] = Provider::class;


spl_autoload_register(function (string $item) {
    if (!\Drupal::hasContainer()) {
        return null;
    }
    $infoParser = new \Drupal\Core\Extension\InfoParser(\Drupal::root());
    $moduleList = \Drupal::moduleHandler()->getModuleList();
    foreach ($moduleList as $module) {
        $info = $infoParser->parse($module->getPathname());
        $files = $info['files'] ?? [];
        foreach ($files as $file) {
            $matches = [];
            $contents = file_get_contents($module->getPath() . '/' . $file);
            $result = preg_match_all('/^\s*(?:abstract|final)?\s*(class|interface|trait)\s+([a-zA-Z0-9_]+)/m', $contents, $matches);
            if ($result !== false) {
                // @todo wrap in closure to remove scope.
                include $module->getPath() . '/' . $file;
            }
            $stop = null;
        }
    }
});
