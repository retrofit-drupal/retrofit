<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Tests\Unit;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccessPolicyProcessorInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\AccountProxy;
use Drupal\Core\Session\CalculatedPermissionsInterface;
use Drupal\Core\Session\CalculatedPermissionsItemInterface;
use Drupal\Core\Session\PermissionChecker;
use Drupal\Core\Session\UserSession;
use Drupal\user\RoleStorageInterface;
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

    public function testUserAccess(): void
    {
        $container = new ContainerBuilder();
        $accountProxy = new AccountProxy(new EventDispatcher());
        $accountProxy->setAccount(new UserSession([
            'uid' => 5,
            'roles' => ['foo'],
        ]));
        $container->set('current_user', $accountProxy);

        $accessPolicyProcessor = $this->createMock(AccessPolicyProcessorInterface::class);
        $accessPolicyProcessor->expects($this->exactly(2))
            ->method('processAccessPolicies')
            ->willReturnCallback(function (AccountInterface $account) {
                $calculatedPermissionsItem = $this->createMock(CalculatedPermissionsItemInterface::class);
                $calculatedPermissionsItem->method('hasPermission')
                    ->with('access content')
                    ->willReturn(in_array('foo', $account->getRoles(), true));
                $calculatedPermissions = $this->createMock(CalculatedPermissionsInterface::class);
                $calculatedPermissions->method('getItem')
                    ->willReturn($calculatedPermissionsItem);
                return $calculatedPermissions;
            });

        $permissionChecker = new PermissionChecker($accessPolicyProcessor);
        $container->set('permission_checker', $permissionChecker);

        \Drupal::setContainer($container);

        self::assertTrue(user_access('access content'));

        $userSession = new UserSession([
            'uid' => 6,
            'roles' => ['bar'],
        ]);
        self::assertFalse(user_access('access content', $userSession));
    }
}
