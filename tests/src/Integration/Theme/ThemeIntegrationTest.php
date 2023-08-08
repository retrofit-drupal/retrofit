<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Tests\Integration\Theme;

use Drupal\Core\DependencyInjection\ContainerBuilder;
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

    protected static $modules = ['system', 'test_page_test'];

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
        assert($themeHandler instanceof ThemeExtensionList);
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

        $this->doRequest(Request::create('/test-page'));
        self::assertStringContainsString(
            'Test page text.',
            $this->getTextContent()
        );
    }
}
