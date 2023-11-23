<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Language;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class GlobalLanguageSetter implements EventSubscriberInterface
{
    public function __construct(
        private readonly LanguageManagerInterface $languageManager,
        private readonly ModuleHandlerInterface $moduleHandler
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
          // LanguageRequestSubscriber::onKernelRequestLanguage runs @ 255.
          KernelEvents::REQUEST => ['setGlobalLanguage', 155],
        ];
    }

    public function setGlobalLanguage(RequestEvent $event): void
    {
        $types = array_map(
            fn($type) => $type === 'language_interface' ? 'language' : $type,
            array_combine($this->languageManager->getLanguageTypes(), $this->languageManager->getLanguageTypes())
        );
        foreach ($this->languageManager->getLanguageTypes() as $type) {
            if (!isset($GLOBALS[$types[$type]])) {
                $language = $this->languageManager->getCurrentLanguage($type);
                $GLOBALS[$types[$type]] = new GlobalLanguage($language);
            }
        }
        if ($this->languageManager->isMultilingual()) {
            // Hooks that completely replace language globals will not work.
            $this->moduleHandler->invokeAllWith('language_init', function (callable $hook, string $module) {
                $hook();
            });
        }
    }
}
