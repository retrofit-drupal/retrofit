<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Language;

use Drupal\Core\Language\LanguageManagerInterface;

final class GlobalLanguageContent
{
    public function __construct(
        private readonly LanguageManagerInterface $languageManager
    ) {
    }

    public function __get(string $name)
    {
        return match ($name) {
            'language' => $this->languageManager->getCurrentLanguage()->getId(),
            default => null
        };
    }
}
