<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Tests\Integration;

use Drupal\KernelTests\KernelTestBase;
use Retrofit\Drupal\Routing\HookMenuRoutes;

final class ProviderTest extends KernelTestBase
{
    public function testRegister(): void
    {
        self::assertTrue($this->container->has(HookMenuRoutes::class));
    }
}
