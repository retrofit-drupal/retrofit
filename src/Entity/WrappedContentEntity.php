<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Entity;

use Drupal\Core\Entity\ContentEntityInterface;

final class WrappedContentEntity
{
    public function __construct(
        private readonly ContentEntityInterface $entity
    ) {
    }

    public function __get(string $name)
    {
        return $this->entity->get($name)->first()?->getValue();
    }

    public function __set(string $name, $value): void
    {
        $this->entity->get($name)->setValue($value);
    }

    public function __isset(string $name): bool
    {
        return isset($this->entity[$name]);
    }
}
