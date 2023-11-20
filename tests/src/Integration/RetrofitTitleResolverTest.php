<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Tests\Integration;

use mglaman\DrupalTestHelpers\RequestTrait;
use Retrofit\Drupal\Controller\RetrofitTitleResolver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Route;

final class RetrofitTitleResolverTest extends IntegrationTestCase
{
    use RequestTrait;

    protected static $modules = [
        'system',
    ];

    protected static function getTestModules(): array
    {
        return ['retrofit_fixtures'];
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->config('system.site')
            ->set('name', 'Drupal')
            ->save();
    }

    public function testDecoration(): void
    {
        self::assertInstanceOf(
            RetrofitTitleResolver::class,
            $this->container->get('title_resolver')
        );
    }

    public function testSetStoredTitle(): void
    {
        $titleResolver = $this->container->get('title_resolver');
        self::assertInstanceOf(
            RetrofitTitleResolver::class,
            $titleResolver
        );
        $requestStack = $this->container->get('request_stack');
        assert($requestStack instanceof RequestStack);
        $request = $requestStack->getCurrentRequest();
        assert($request instanceof Request);
        self::assertEquals(null, $titleResolver->getTitle(
            $request,
            $this->createMock(Route::class)
        ));
        $titleResolver->setStoredTitle('foo');
        self::assertEquals('foo', $titleResolver->getTitle(
            $request,
            $this->createMock(Route::class)
        ));
    }

    public function testRoute(): void
    {
        $this->doRequest(Request::create('/retrofit/drupal_set_title'));
        $this->assertTitle('this title has been overridden | Drupal');
    }
}
