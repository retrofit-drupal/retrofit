<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Entity;

class Role extends \Drupal\user\Entity\Role
{
    public function __toString()
    {
        return $this->label;
    }

    public function __set(string $name, string $value): void
    {
        match ($name) {
            'rid' => $this->id = $value,
            'name' => $this->label = $value,
            default => null
        };
    }

    public function __get(string $name): mixed
    {
        return match ($name) {
            'rid' => $this->id,
            'name' => $this->label,
            default => null
        };
    }

    public function __isset(string $name): bool
    {
        return match ($name) {
            'rid', 'name' => true,
            default => false
        };
    }
}
