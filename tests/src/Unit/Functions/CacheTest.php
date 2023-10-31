<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Tests\Unit\Functions;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class CacheTest extends TestCase
{
    public function testLegacyGenericBinToDefault(): void
    {
        $cacheBackend = $this->createMock(CacheBackendInterface::class);
        $container = $this->createMock(ContainerInterface::class);
        $container
            ->expects($this->exactly(6))
            ->method('get')
            ->with('cache.default')
            ->willReturn($cacheBackend);
        \Drupal::setContainer($container);

        cache_get('foo');
        cache_get('foo', 'cache');

        $cids = ['1'];
        cache_get_multiple($cids);
        cache_get_multiple($cids, 'cache');

        cache_set('abc', 'def');
        cache_set('abc', 'def', 'cache');
    }
}
