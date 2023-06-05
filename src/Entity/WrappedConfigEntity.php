<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

final class WrappedConfigEntity
{
    public function __construct(
        private readonly ConfigEntityInterface $entity
    ) {
    }


    public function __get(string $name)
    {
        return $this->entity->get($name);
    }

    public function __set(string $name, $value): void
    {
        $this->entity->set($name, $value);
    }

    public function __isset(string $name): bool
    {
        return isset($this->entity[$name]);
    }
}
