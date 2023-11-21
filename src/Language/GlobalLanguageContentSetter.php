<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Language;

use Drupal\Core\Language\LanguageManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class GlobalLanguageContentSetter implements EventSubscriberInterface
{
    public function __construct(
        private readonly LanguageManagerInterface $languageManager
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
          // LanguageRequestSubscriber::onKernelRequestLanguage runs @ 255.
          KernelEvents::REQUEST => ['setGlobalLanguageContent', 155],
        ];
    }

    public function setGlobalLanguageContent(RequestEvent $event): void
    {
        if (!isset($GLOBALS['language'])) {
            $GLOBALS['language'] = new GlobalLanguageContent($this->languageManager);
        }
    }
}
