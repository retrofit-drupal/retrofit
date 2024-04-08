<?php

declare(strict_types=1);

namespace Retrofit\Drupal\EventSubscriber;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class HookInit implements EventSubscriberInterface
{
    public function __construct(
        private readonly ModuleHandlerInterface $moduleHandler,
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['hookInit', 200],
        ];
    }

    public function hookInit(RequestEvent $event): void
    {
        $this->moduleHandler->invokeAll('init');
    }
}
