<?php

declare(strict_types=1);

namespace Retrofit\Drupal\DependencyInjection\Compiler;

use Drupal\Core\Extension\InfoParser;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class FilesAutoloaderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $filesAutoloadRegistry = [];
        $appRoot = $container->getParameter('app.root');
        assert(is_string($appRoot) || is_null($appRoot));
        $infoParser = new InfoParser($appRoot);
        /** @var array<string, array{pathname: string}> $container_modules */
        $container_modules = $container->getParameter('container.modules');
        foreach ($container_modules as $module) {
            $info = $infoParser->parse($module['pathname']);
            $files = $info['files'] ?? [];
            foreach ($files as $file) {
                $matches = [];
                $file_path = dirname($module['pathname']) . '/' . $file;
                $contents = file_get_contents($file_path);
                if ($contents === false) {
                    continue;
                }
                $result = preg_match_all(
                    '/^\s*(?:abstract|final)?\s*(class|interface|trait)\s+([a-zA-Z0-9_]+)/m',
                    $contents,
                    $matches
                );
                if ($result !== false) {
                    foreach ($matches[2] as $name) {
                        $filesAutoloadRegistry[$name] = $file_path;
                    }
                }
            }
        }
        $container->setParameter('files_autoload_registry', $filesAutoloadRegistry);
    }
}
