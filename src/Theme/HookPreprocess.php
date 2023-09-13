<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Theme;

use Retrofit\Drupal\Entity\WrappedConfigEntity;

/**
 * @phpstan-type Variables array<string, string|array<int|string, mixed>>
 * @phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
 */
final class HookPreprocess
{
    /**
     * @param Variables $variables
     */
    public static function page(array &$variables): void
    {
        $variables['logo'] = theme_get_setting('logo.url');

        // @todo support in https://github.com/retrofit-drupal/retrofit/issues/43
        $variables['main_menu'] = [];
        $variables['secondary_menu'] = [];

        // Legacy variables replaced by blocks.
        $variables['title'] = $variables['page']['#title'] ?? '';
        $variables['breadcrumb'] = '';
        $variables['messages'] = '';
        $variables['tabs'] = '';
        $variables['action_links'] = '';
        $variables['feed_icons'] = '';
    }

    /**
     * @param Variables $variables
     */
    public static function maintenance_page(array &$variables): void
    {
        self::page($variables);
    }

    /**
     * @param Variables $variables
     */
    public static function block(array &$variables): void
    {
        // @todo find a way to do this earlier.
        // \Drupal\block\BlockViewBuilder::preRender removes the block
        // after building the plugin. This is within a lazy builder
        // that is called through a class name and method callable,
        // not a service.
        $block = \Drupal::entityTypeManager()
            ->getStorage('block')
            ->load($variables['elements']['#id']);
        $variables['block'] = new WrappedConfigEntity($block);
    }
}
