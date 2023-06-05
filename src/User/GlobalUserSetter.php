<?php

declare(strict_types=1);

namespace Retrofit\Drupal\User;

use Drupal\Core\Session\AccountEvents;
use Drupal\Core\Session\AccountSetEvent;
use Drupal\Core\Session\AnonymousUserSession;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class GlobalUserSetter implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        $events = [];
        // Set global user on every request.
        $events[KernelEvents::REQUEST][] = ['onKernelRequestAuthenticate', 500];
        $events[AccountEvents::SET_USER] = ['onAccountSetUser', 100];
        return $events;
    }

    public function onKernelRequestAuthenticate(RequestEvent $event)
    {
        if (!isset($GLOBALS['user'])) {
            $GLOBALS['user'] = new GlobalUser(new AnonymousUserSession());
        }
    }

    public function onAccountSetUser(AccountSetEvent $event)
    {
        $GLOBALS['user'] = new GlobalUser($event->getAccount());
    }
}
