<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Extension;

use Drupal\Core\Extension\Extension;

class ExtensionWrapper
{
    final public function __construct(
        private readonly Extension $inner,
    ) {
    }

    public static function create(Extension $inner): static
    {
        return new static($inner);
    }

    public function __set(string $name, mixed $value): void
    {
        $this->inner->$name = $value;
    }

    public function __get(string $name): mixed
    {
        return match ($name) {
            'filename' => $this->inner->getPathname(),
            'name' => $this->inner->getName(),
            'type' => $this->inner->getType(),
            default => $this->inner->$name,
        };
    }

    public function __isset(string $name): bool
    {
        return match ($name) {
            'filename', 'name', 'type' => true,
            default => isset($this->inner->$name),
        };
    }

    public function __unset(string $name): void
    {
        unset($this->inner->$name);
    }

    /**
     * @param mixed[] $arguments
     */
    public function __call(string $name, array $arguments): mixed
    {
        return $this->inner->$name($arguments);
    }
}
