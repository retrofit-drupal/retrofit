<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Tests\Integration\Template;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use mglaman\DrupalTestHelpers\RequestTrait;
use mglaman\DrupalTestHelpers\TestHttpKernelTrait;
use Retrofit\Drupal\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Request;

final class HookThemeTest extends IntegrationTestCase
{
    use RequestTrait;
    use TestHttpKernelTrait;

    protected static $modules = ['system'];

    public function register(ContainerBuilder $container)
    {
        parent::register($container);
        $this->registerTestHttpKernel($container);
        $twig = $container->getParameter('twig.config');
        $twig['debug'] = true;
        $container->setParameter('twig.config', $twig);
    }

    protected static function getTestModules(): array
    {
        return ['theming_example'];
    }

    public function testHookTheme(): void
    {
        $this->doRequest(Request::create('/examples/theming_example'));
        self::assertStringContainsString(
            '<p><strong>Some examples of pages and forms that are run through theme functions.</strong></p>',
            $this->getRawContent()
        );
        self::assertStringContainsString(
            '<p><a href="/examples/theming_example/theming_example_list_page">Simple page with a list</a></p>',
            $this->getRawContent()
        );
    }
}
