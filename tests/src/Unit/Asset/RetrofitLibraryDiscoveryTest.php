<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Tests\Unit\Asset;

use Drupal\Core\Asset\LibraryDiscoveryInterface;
use PHPUnit\Framework\TestCase;
use Retrofit\Drupal\Asset\RetrofitLibraryDiscovery;

/**
 * @coversDefaultClass \Retrofit\Drupal\Asset\RetrofitLibraryDiscovery
 */
final class RetrofitLibraryDiscoveryTest extends TestCase
{

    /**
     * @covers ::__construct
     * @covers ::getLibrariesByExtension
     * @covers ::setRetrofitLibrary
     */
    public function testGetLibrariesByExtension(): void
    {
        $inner = $this->createMock(LibraryDiscoveryInterface::class);
        $inner->expects($this->once())
            ->method('getLibrariesByExtension')
            ->with('system')
            ->willReturn(['drupal.system' => []]);
        $sut = new RetrofitLibraryDiscovery($inner);
        self::assertEquals([], $sut->getLibrariesByExtension('retrofit'));
        self::assertEquals(
            ['drupal.system' => []],
            $sut->getLibrariesByExtension('system')
        );

        $sut->setRetrofitLibrary('foo', []);
        self::assertEquals(
            [
                'foo' => ['license' => [],]
            ],
            $sut->getLibrariesByExtension('retrofit')
        );
    }

    /**
     * @covers ::__construct
     * @covers ::getLibraryByName
     * @covers ::setRetrofitLibrary
     */
    public function testGetLibraryByName(): void
    {
        $inner = $this->createMock(LibraryDiscoveryInterface::class);
        $inner->expects($this->once())
            ->method('getLibraryByName')
            ->with('system', 'drupal.system')
            ->willReturn([]);
        $sut = new RetrofitLibraryDiscovery($inner);
        self::assertFalse($sut->getLibraryByName('retrofit', 'foo'));
        self::assertEquals(
            [],
            $sut->getLibraryByName('system', 'drupal.system')
        );

        $sut->setRetrofitLibrary('foo', []);
        self::assertEquals(
            [
                'license' => [],
            ],
            $sut->getLibraryByName('retrofit', 'foo')
        );
    }

    /**
     * @covers ::__construct
     * @covers ::clearCachedDefinitions
     */
    public function testClearCachedDefinitions(): void
    {
        $inner = $this->createMock(LibraryDiscoveryInterface::class);
        $inner->expects($this->once())
            ->method('clearCachedDefinitions');
        $sut = new RetrofitLibraryDiscovery($inner);
        $sut->clearCachedDefinitions();
    }

    /**
     * @dataProvider retrofitLibraryData
     */
    public function testSetRetrofitLibrary(string $key, array $attachments, array $expected): void
    {
        $sut = new RetrofitLibraryDiscovery(
            $this->createMock(LibraryDiscoveryInterface::class)
        );
        $sut->setRetrofitLibrary($key, $attachments);
        self::assertEquals(
            [$key => $expected],
            $sut->getLibrariesByExtension('retrofit')
        );
    }

    public static function retrofitLibraryData(): \Generator
    {
        yield [
            'bar',
            [],
            [
                'license' => [],
            ]
        ];
    }

}
