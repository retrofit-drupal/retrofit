<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Tests\Integration\Menu;

use Retrofit\Drupal\Tests\Integration\IntegrationTestCase;

final class MenuLinkManagerTest extends IntegrationTestCase
{
    protected static $modules = ['system'];

    protected static function getTestModules(): array
    {
        return ['menu_example'];
    }

    public function testMenuLinks(): void
    {
        $this->container->get('plugin.manager.menu.link')->rebuild();
        $menuLinkTree = $this->container->get('menu.link_tree');
        $parameters = $menuLinkTree->getCurrentRouteMenuTreeParameters('main-menu');
        $tree = $menuLinkTree->load('main', $parameters);
        self::assertCount(1, $tree);
        $manipulators = [
          ['callable' => 'menu.default_tree_manipulators:generateIndexAndSort'],
        ];
        $tree = $menuLinkTree->transform($tree, $manipulators);
        $build = $menuLinkTree->build($tree);
        $this->render($build);
        $this->assertLink('Menu Example: Menu in alternate menu');
        $this->assertLinkByHref('/examples/menu_example_alternate_menu');
    }
}
