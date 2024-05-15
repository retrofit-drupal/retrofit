<?php

declare(strict_types=1);

namespace Retrofit\Drupal\DependencyInjection\Compiler;

use Drupal\Core\Extension\InfoParser;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class FilesAutoloaderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $filesAutoloadRegistry = [];
        $appRoot = $container->getParameter('app.root');
        assert(is_string($appRoot) || is_null($appRoot));
        $infoParser = new InfoParser($appRoot);
        /** @var array<string, array{pathname: string}> $container_modules */
        $container_modules = $container->getParameter('container.modules');
        $files = [];
        foreach ($container_modules as $name => $module) {
            $modulePath = dirname($module['pathname']);
            $info = $infoParser->parse($module['pathname']);
            $files[] = array_map(
                static fn(string $file) => "$modulePath/$file",
                $info['files'] ?? []
            );

            $legacyInfoFilePath = "$modulePath/$name.info";
            if (file_exists($legacyInfoFilePath)) {
                $legacyInfoFile = file_get_contents($legacyInfoFilePath);
                if ($legacyInfoFile !== false) {
                    $legacyInfo = drupal_parse_info_format($legacyInfoFile);
                    $files[] = array_map(
                        static fn(string $file) => "$modulePath/$file",
                        $legacyInfo['files'] ?? []
                    );
                }
            }
        }
        $files = array_unique(array_merge(...$files));
        foreach ($files as $file) {
            $matches = [];
            $contents = file_get_contents($file);
            if ($contents === false) {
                continue;
            }
            $result = preg_match_all(
                '/^\s*(?:abstract|final)?\s*(class|interface|trait)\s+([a-zA-Z0-9_]+)/m',
                $contents,
                $matches
            );
            if ($result !== false) {
                foreach ($matches[2] as $itemName) {
                    $filesAutoloadRegistry[$itemName] = $file;
                }
            }
        }
        $container->setParameter('files_autoload_registry', $filesAutoloadRegistry);
    }
}
