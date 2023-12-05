<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Theme;

use Drupal\Core\Theme\ActiveTheme;
use Drupal\Core\Theme\Registry as CoreRegistry;
use Drupal\Core\Utility\ThemeRegistry;

/**
 * @phpstan-type RegistryData array<string, array{
 *     type: string,
 *     template: string,
 *     path: string,
 *    'preprocess functions': callable[],
 * }>
 */
final class Registry extends CoreRegistry
{
    /**
     * @param RegistryData $cache
     */
    protected function processExtension(array &$cache, $name, $type, $theme, $path): void
    {
        parent::processExtension($cache, $name, $type, $theme, $path);

        foreach ($cache as $theme_hook => $info) {
            if (method_exists(HookPreprocess::class, $theme_hook)) {
                $cache[$theme_hook]['preprocess functions'][] = HookPreprocess::class . '::' . $theme_hook;
            }
            if ($type === 'module') {
                $theme_function = 'theme_' . $theme_hook;
                if (function_exists($theme_function)) {
                    $cache[$theme_hook]['template'] = 'theme-function';
                    $cache[$theme_hook]['path'] = '@retrofit';
                } else {
                    // In Drupal 7, the `path` did not append the `templates` directory
                    // if the `path` was not set. In Drupal 8 this changed to follow community
                    // practices of putting all templates under the `template` directory.
                    $path = $info['path'] . '/' . $info['template'] . '.tpl.php';
                    $themePath = $info['theme path'] . '/' . $info['template'] . '.tpl.php';
                    if (file_exists($path)) {
                        $cache[$theme_hook]['phptemplate'] = $path;
                        $cache[$theme_hook]['template'] = 'theme-phptemplate';
                        $cache[$theme_hook]['path'] = '@retrofit';
                    } elseif (file_exists($themePath)) {
                        $cache[$theme_hook]['phptemplate'] = $themePath;
                        $cache[$theme_hook]['template'] = 'theme-phptemplate';
                        $cache[$theme_hook]['path'] = '@retrofit';
                    }
                }
            }
        }

        if ($type === 'theme_engine') {
            $templates = drupal_find_theme_templates($cache, '.tpl.php', $path);
            foreach ($templates as $theme_hook => $info) {
                $cache[$theme_hook]['phptemplate'] = $info['path'] . '/' . $info['template'] . '.tpl.php';
                $cache[$theme_hook]['template'] = 'theme-phptemplate';
                $cache[$theme_hook]['path'] = '@retrofit';
            }
        }

        $cache['form_required_marker'] = [
            'render element' => 'element',
            'type' => 'module',
            'template' => 'theme-function',
            'path' => '@retrofit',
        ];
    }

    /**
     * @param array<string, array{'preprocess functions': callable[]}> $cache
     */
    protected function postProcessExtension(array &$cache, ActiveTheme $theme): void
    {
        parent::postProcessExtension($cache, $theme);
        // Add all `hook_process_HOOK` as preprocess functions.
        $prefixes = array_keys($this->moduleHandler->getModuleList());
        foreach (array_reverse($theme->getBaseThemeExtensions()) as $base) {
            $prefixes[] = $base->getName();
        }
        if ($theme->getEngine()) {
            $prefixes[] = $theme->getEngine() . '_engine';
        }
        $prefixes[] = $theme->getName();

        foreach ($cache as $hook => $info) {
            foreach ($prefixes as $prefix) {
                if (function_exists($prefix . '_process')) {
                    $cache[$hook]['preprocess functions'][] = $prefix . '_process';
                }
                if (function_exists($prefix . '_process_' . $hook)) {
                    $cache[$hook]['preprocess functions'][] = $prefix . '_process_' . $hook;
                }
            }
        }
    }
}
