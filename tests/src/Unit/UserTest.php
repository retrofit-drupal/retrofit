<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Tests\Unit;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Session\AccountProxy;
use Drupal\Core\Session\UserSession;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

final class UserTest extends TestCase
{
    public function testUserIsAnonymous(): void
    {
        $container = new ContainerBuilder();
        $container->set('current_user', new AccountProxy(new EventDispatcher()));
        \Drupal::setContainer($container);

        self::assertTrue(user_is_anonymous());
    }

    public function testUserIsAuthenticated(): void
    {
        $container = new ContainerBuilder();
        $accountProxy = new AccountProxy(new EventDispatcher());
        $accountProxy->setAccount(new UserSession([
            'uid' => 5,
        ]));
        $container->set('current_user', $accountProxy);
        \Drupal::setContainer($container);

        self::assertTrue(user_is_logged_in());
    }
}
