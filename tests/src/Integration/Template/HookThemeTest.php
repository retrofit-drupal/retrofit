<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Tests\Integration\Template;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Tests\user\Traits\UserCreationTrait;
use mglaman\DrupalTestHelpers\RequestTrait;
use mglaman\DrupalTestHelpers\TestHttpKernelTrait;
use Retrofit\Drupal\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Request;

final class HookThemeTest extends IntegrationTestCase
{
    use RequestTrait;
    use TestHttpKernelTrait;
    use UserCreationTrait;

    /** @var string[]  */
    protected static $modules = ['system', 'user'];

    public function register(ContainerBuilder $container): void
    {
        parent::register($container);
        /** @var array{debug: bool} $twig */
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

    public function testThemingExampleListPage(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('You are not allowed to use css in #attached.');

        $this->setUpCurrentUser([], ['access content']);
        $this->doRequest(Request::create('/examples/theming_example/theming_example_list_page'));
    }

    public function testThemingExampleSelectForm(): void
    {
        $this->setUpCurrentUser([], ['access content']);
        $this->doRequest(Request::create('/examples/theming_example/theming_example_select_form'));
        self::assertStringContainsString(
            '<strong>Choose which ordering you want</strong>',
            $this->getRawContent(),
        );

        $this->doFormSubmit('/examples/theming_example/theming_example_select_form', [
            'choice' => 'edited_first',
            'op' => 'Go',
        ]);
        self::assertStringContainsString(
            'You chose edited_first',
            $this->getTextContent(),
        );
    }

    public function testThemingExampleTextForm(): void
    {
        $this->setUpCurrentUser([], ['access content']);
        $this->doRequest(Request::create('/examples/theming_example/theming_example_text_form'));
        self::assertStringContainsString(
            '<!-- theming-example-text-form template -->',
            $this->getRawContent(),
        );
        self::assertStringContainsString(
            '<!-- /theming-example-text-form template -->',
            $this->getRawContent(),
        );

        var_export($this->getRawContent());

        $this->doFormSubmit('/examples/theming_example/theming_example_text_form', [
            'text' => 'Some random testing text!',
            'op' => 'Go',
        ]);
        self::assertStringContainsString(
            'You entered Some random testing text!',
            $this->getTextContent(),
        );
    }
}
