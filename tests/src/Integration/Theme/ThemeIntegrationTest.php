<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Tests\Integration\Theme;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ThemeExtensionList;
use mglaman\DrupalTestHelpers\RequestTrait;
use mglaman\DrupalTestHelpers\TestHttpKernelTrait;
use Retrofit\Drupal\Tests\Integration\IntegrationTestCase;
use Retrofit\Drupal\Tests\Utils\TestThemeExtensionList;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\HttpFoundation\Request;

final class ThemeIntegrationTest extends IntegrationTestCase
{
    use RequestTrait;
    use TestHttpKernelTrait;

    protected static $modules = [
        'system',
        'block',
        'test_page_test'
    ];

    protected $strictConfigSchema = false;

    public function register(ContainerBuilder $container): void
    {
        parent::register($container);
        $container->setDefinition(
            TestThemeExtensionList::class,
            (new ChildDefinition('extension.list.theme'))
                ->setDecoratedService('extension.list.theme')
        );
        $this->registerTestHttpKernel($container);
        /** @var array{debug: bool} $twig */
        $twig = $container->getParameter('twig.config');
        $twig['debug'] = true;
        $container->setParameter('twig.config', $twig);
    }

    public function testListInfo(): void
    {
        $themeHandler = $this->container->get('extension.list.theme');
        self::assertArrayHasKey('bartik', $themeHandler->getList());
    }

    public function testPageDoesNotCrashWithTheme(): void
    {
        $this->config('core.extension')
            ->set('theme.bartik', 0)
            ->save(true);
        $this->config('system.theme')
            ->set('default', 'bartik')
            ->save(true);

        $this->placeBlock('page_title_block', 'header');

        try {
            $this->doRequest(Request::create('/test-page'));
            self::assertStringContainsString(
                'Test page text.',
                $this->getTextContent()
            );
            self::assertStringContainsString(
                '<div id="page-wrapper"><div id="page">',
                $this->getRawContent()
            );
            self::assertStringContainsString(
                '<h1class="title"id="page-title">Testpage</h1>',
                preg_replace('/\s/', '', $this->getRawContent())
            );
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    private function placeBlock(string $plugin_id, string $region): void
    {
        $entity_type_manager = $this->container->get('entity_type.manager');
        $block_storage = $entity_type_manager->getStorage('block');
        $block_storage->create([
            'id' => "test_$plugin_id",
            'theme' => 'bartik',
            'region' => $region,
            'plugin' => $plugin_id,
            'settings' => [
                'id' => $plugin_id,
                'label' => $this->randomMachineName()
            ],
            'visibility' => [],
        ])->save();
    }
}
