<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Theme;

final class HookPreprocess
{

    /**
     * @param array<string, array|string> $variables
     */
    public static function page(array &$variables): void
    {
        $variables['logo'] = theme_get_setting('logo.url');

        // @todo support in https://github.com/mglaman/retrofit-drupal/issues/43
        $variables['main_menu'] = [];
        $variables['secondary_menu'] = [];

        // Legacy variables replaced by blocks.
        $variables['title'] = '';
        $variables['breadcrumb'] = '';
        $variables['messages'] = '';
        $variables['tabs'] = '';
        $variables['action_links'] = '';
        $variables['feed_icons'] = '';
    }

    /**
     * @param array<string, array|string> $variables
     */
    public static function maintenance_page(array &$variables): void
    {
        self::page($variables);
    }

}
