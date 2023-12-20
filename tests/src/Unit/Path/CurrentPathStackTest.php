<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Tests\Unit\Path;

use PHPUnit\Framework\TestCase;
use Retrofit\Drupal\Path\CurrentPathStack;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @coversDefaultClass \Retrofit\Drupal\Path\CurrentPathStack
 */
final class CurrentPathStackTest extends TestCase
{
    protected function tearDown(): void
    {
        // phpcs:ignore
        unset($_GET['q']);
    }

    /**
     * @covers ::getPath
     */
    public function testGetPath(): void
    {
        $request1 = Request::create('/foo');
        $request2 = Request::create('/bar');
        $requestStack = new RequestStack();
        $requestStack->push($request1);
        $sut = new CurrentPathStack($requestStack);
        self::assertEquals('/foo', $sut->getPath());
        self::assertEquals('/bar', $sut->getPath($request2));
        $requestStack->push($request2);
        self::assertEquals('/foo', $sut->getPath($request1));
        self::assertEquals('/bar', $sut->getPath());
    }

    /**
     * @covers ::setPath
     */
    public function testSetPath(): void
    {
        $path = '/node/1';
        $request = Request::create('/test-page');
        $requestStack = new RequestStack();
        $requestStack->push($request);

        $sut = new CurrentPathStack($requestStack);

        self::assertArrayNotHasKey('q', $_GET);
        $sut->setPath($path, $request);
        self::assertArrayHasKey('q', $_GET);
        self::assertEquals($path, $_GET['q']);
    }

    /**
     * @covers ::setPath
     * @covers ::getPath
     */
    public function testMutable(): void
    {
        $request = Request::create('/test-page');
        $requestStack = new RequestStack();
        $requestStack->push($request);

        $sut = new CurrentPathStack($requestStack);
        $sut->setPath('/node/1', $request);
        $_GET['q'] = '/node/2';
        self::assertEquals('/node/2', $_GET['q']);
        self::assertEquals('/node/2', $sut->getPath());
    }
}
