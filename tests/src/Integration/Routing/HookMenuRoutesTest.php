<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Tests\Integration\Routing;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Http\Exception\CacheableAccessDeniedHttpException;
use Drupal\Tests\user\Traits\UserCreationTrait;
use Retrofit\Drupal\Tests\Integration\IntegrationTestCase;
use mglaman\DrupalTestHelpers\RequestTrait;
use mglaman\DrupalTestHelpers\TestHttpKernelTrait;
use Symfony\Component\HttpFoundation\Request;

final class HookMenuRoutesTest extends IntegrationTestCase
{
    use RequestTrait;
    use TestHttpKernelTrait;
    use UserCreationTrait;

    protected static $modules = [
      'system',
      'user',
    ];

    protected static function getTestModules(): array
    {
        return ['menu_example'];
    }

    public function register(ContainerBuilder $container): void
    {
        parent::register($container);
        $this->registerTestHttpKernel($container);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->installConfig(['user']);
        $this->config('system.site')
          ->set('name', 'Drupal')
          ->save();
    }

    public function testMenuExample(): void
    {
        $response = $this->doRequest(Request::create('/examples/menu_example'));
        self::assertEquals(200, $response->getStatusCode());
        $this->assertTitle('Menu Example | Drupal');
        self::assertStringContainsString(
            '<div>This is the base page of the Menu Example. There are a number of examples
  here, from the most basic (like this one) to extravagant mappings of loaded
  placeholder arguments. Enjoy!</div>',
            $response->getContent()
        );
    }

    public function testMenuExamplePermissioned(): void
    {
        try {
            $this->doRequest(Request::create('/examples/menu_example/permissioned/controlled'));
            self::fail('Route should have failed on missing permissions');
        } catch (CacheableAccessDeniedHttpException $e) {
            self::assertEquals(
                "The 'access protected menu example' permission is required.",
                $e->getMessage()
            );
        }
        $this->setUpCurrentUser([], ['access protected menu example']);
        $response = $this->doRequest(Request::create('/examples/menu_example/permissioned/controlled'));
        self::assertEquals(200, $response->getStatusCode());
        $this->assertTitle('Permissioned Menu Item | Drupal');
        self::assertStringContainsString(
            // phpcs:ignore Generic.Files.LineLength.TooLong
            '<div>This menu entry will not show and the page will not be accessible without the "access protected menu example" permission.</div>',
            $response->getContent()
        );
    }

    public function testMenuExampleCustomAccess(): void
    {
        try {
            $this->doRequest(Request::create('/examples/menu_example/custom_access/page'));
        } catch (CacheableAccessDeniedHttpException) {
        }
        $this->setUpCurrentUser();
        $response = $this->doRequest(Request::create('/examples/menu_example/custom_access/page'));
        self::assertStringContainsString(
            // phpcs:ignore Generic.Files.LineLength.TooLong
            'This menu entry will not be visible and access will result in a 403 error unless the user has the "authenticated user" role. This is accomplished with a custom access callback.',
            $response->getContent()
        );
    }

    public function testMenuExampleTitleCallbacks(): void
    {
        $this->doRequest(Request::create('/examples/menu_example/title_callbacks'));
        $this->assertTitle('Dynamic title: username= anonymous | Drupal');

        $user = $this->setUpCurrentUser();
        $this->doRequest(Request::create('/examples/menu_example/title_callbacks'));
        $this->assertTitle("Dynamic title: username= {$user->getAccountName()} | Drupal");
    }

    public function testMenuExamplePlaceholderArgument(): void
    {
        $response = $this->doRequest(Request::create('/examples/menu_example/placeholder_argument/fooBarBaz/display'));
        $this->assertTitle('Placeholder Arguments | Drupal');
        self::assertStringContainsString(
            '<div>fooBarBaz</div>',
            $response->getContent()
        );
    }

    public function testMenuExamplePlaceholderArgumentWithLoad(): void
    {
        $response = $this->doRequest(Request::create('/examples/menu_example/default_arg/99'));
        $this->assertTitle('Processed Placeholder Arguments | Drupal');
        self::assertStringContainsString(
            '<div>Loaded value was <em class="placeholder">jackpot! default</em></div>',
            $response->getContent()
        );
        $response = $this->doRequest(Request::create('/examples/menu_example/default_arg/doesNotExist'));
        $this->assertTitle('Processed Placeholder Arguments | Drupal');
        self::assertStringContainsString(
            '<div>Sorry, the id <em class="placeholder">doesNotExist</em> was not found to be loaded</div>',
            $response->getContent()
        );
    }

    // @todo next test for path `examples/menu_example/menu_original_path` on alter.
}
