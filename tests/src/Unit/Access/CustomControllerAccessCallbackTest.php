<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Tests\Unit\Access;

use Drupal\Core\Routing\RouteMatch;
use PHPUnit\Framework\TestCase;
use Retrofit\Drupal\Access\CustomControllerAccessCallback;
use Symfony\Component\Routing\Route;

final class CustomControllerAccessCallbackTest extends TestCase
{
    /**
     * @param string|null|array<int, mixed> $arguments
     * @dataProvider routeAccessCallbackData
     */
    public function testCustomAccessCallbackMissing(
        ?string $callback,
        string|null|array $arguments,
        bool $allowed
    ): void {
        $route = new Route('/foo');
        $route->setDefault('_custom_access_callback', $callback);
        $route->setDefault('_custom_access_arguments', $arguments);
        $routeMatch = new RouteMatch('foo', $route);

        $sut = new CustomControllerAccessCallback();
        $result = $sut->check($routeMatch);
        self::assertEquals($allowed, $result->isAllowed());
    }

    public static function routeAccessCallbackData(): \Generator
    {
        yield 'null callback does not crash' => [null, null, false];
        yield 'null arguments do not crash' => [
            '\Retrofit\Drupal\Tests\Unit\Access\CustomControllerAccessCallbackTest::callbackFixture',
            null,
            true
        ];
        yield 'invalid callback is not allowed' => [
            '\Retrofit\Drupal\Tests\Unit\Access\CustomControllerAccessCallbackTest::callbackFixtureDoesNotExist',
            null,
            false
        ];
        yield 'invalid arguments is not allowed' => [
            '\Retrofit\Drupal\Tests\Unit\Access\CustomControllerAccessCallbackTest::callbackFixture',
            'foo',
            false
        ];
    }

    public static function callbackFixture(): bool
    {
        return true;
    }
}
