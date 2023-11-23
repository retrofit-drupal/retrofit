<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Language;

use Drupal\Core\Language\LanguageInterface;

final class GlobalLanguage
{
    /**
     * @var string
     */
    protected $innerClass;

    public function __construct(
        private readonly LanguageInterface $inner
    ) {
        $this->innerClass = get_class($inner);
    }

    public function __set(string $name, mixed $value): void
    {
        assert(method_exists($this->inner, '__construct'));
        match (true) {
            $name === 'language' => $this->inner->__construct(['id' => $value]),
            property_exists($this->innerClass, $name) => $this->inner->__construct([$name => $value]),
            default => $this->inner->$name = $value,
        };
    }

    public function __get(string $name): mixed
    {
        return match ($name) {
            'language' => $this->inner->getId(),
            'name' => $this->inner->getName(),
            'direction' => $this->inner->getDirection(),
            'weight' => $this->inner->getWeight(),
            'locked' => $this->inner->isLocked(),
            default => $this->inner->$name,
        };
    }

    public function __isset(string $name): bool
    {
        return match (true) {
            $name === 'language' => true,
            default => property_exists($this->innerClass, $name) || isset($this->inner->$name),
        };
    }

    public function __unset(string $name): void
    {
        if ($name !== 'language' && !property_exists($this->innerClass, $name)) {
            unset($this->inner->$name);
        }
    }
}
