<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Theme;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Theme\Registry as CoreRegistry;

final class Registry extends CoreRegistry
{
    protected function processExtension(
        array &$cache,
        $name,
        $type,
        $theme,
        $path
    ) {
        parent::processExtension(
            $cache,
            $name,
            $type,
            $theme,
            $path
        );
        if ($type === 'module') {
            foreach ($cache as $theme_hook => $info) {
                $theme_function = 'theme_' . $theme_hook;
                if (function_exists($theme_function)) {
                    $cache[$theme_hook]['template'] = 'theme-function';
                    $cache[$theme_hook]['path'] = '@retrofit';
                } else {
                    // @todo check if Twig template exists or not. If not, then
                    //   check if a .tpl.php template exists. Then add a layer
                    //   for rendering the template via Twig function.
                }
            }
        }
    }
}
