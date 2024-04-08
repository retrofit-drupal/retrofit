<?php

declare(strict_types=1);

namespace Retrofit\Drupal\EventSubscriber;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class HookExit implements EventSubscriberInterface
{
    public function __construct(
        private readonly ModuleHandlerInterface $moduleHandler,
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::TERMINATE => 'hookExit',
        ];
    }

    public function hookExit(TerminateEvent $event): void
    {
        $this->moduleHandler->invokeAll('exit');
    }
}
