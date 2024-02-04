<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Tests\Unit\Asset;

use PHPUnit\Framework\TestCase;

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
        $this->markTestIncomplete();
    }

    /**
     * @covers ::__construct
     * @covers ::getLibraryByName
     * @covers ::setRetrofitLibrary
     */
    public function testGetLibraryByName(): void
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers ::__construct
     * @covers ::clearCachedDefinitions
     */
    public function testClearCachedDefinitions(): void
    {
        $this->markTestIncomplete();
    }

}
