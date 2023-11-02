<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Tests\Unit;

use Drupal\Core\Lock\LockBackendInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class LockTest extends TestCase
{
    private function setMockedLockBackend(LockBackendInterface $backend): void
    {
        $container = new ContainerBuilder();
        $container->set('lock', $backend);
        \Drupal::setContainer($container);
    }
    public function testLockAcquire(): void
    {
        $lock = $this->createMock(LockBackendInterface::class);
        $lock->expects($this->once())
            ->method('acquire')
            ->willReturn(true);
        $this->setMockedLockBackend($lock);
        $this->assertTrue(lock_acquire('foo'));
    }

    public function testLockMayBeAvailable(): void
    {
        $lock = $this->createMock(LockBackendInterface::class);
        $lock->expects($this->once())
            ->method('lockMayBeAvailable')
            ->willReturn(false);
        $this->setMockedLockBackend($lock);
        $this->assertFalse(lock_may_be_available('foo'));
    }

    public function testLockWait(): void
    {
        $lock = $this->createMock(LockBackendInterface::class);
        $lock->expects($this->exactly(2))
            ->method('wait')
            ->willReturnMap([
                ['foo', 30, false],
                ['bar', 15, false],
            ]);
        $this->setMockedLockBackend($lock);
        $this->assertFalse(lock_wait('foo'));
        $this->assertFalse(lock_wait('bar', 15));
    }

    public function testLockRelease(): void
    {
        $lock = $this->createMock(LockBackendInterface::class);
        $lock->expects($this->once())
            ->method('release')
            ->with('foo');
        $this->setMockedLockBackend($lock);
        lock_release('foo');
    }

    public function testLockReleaseAll(): void
    {
        $lock = $this->createMock(LockBackendInterface::class);
        $lock->expects($this->exactly(2))
            ->method('releaseAll')
            ->willReturnMap([
                [null],
                ['bar'],
            ]);
        $this->setMockedLockBackend($lock);
        lock_release_all();
        lock_release_all('bar');
    }
}
