<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Form;

use Drupal\Core\Form\FormState;

final class ArrayAccessFormState extends FormState implements \ArrayAccess
{
    public function offsetExists(mixed $offset): bool
    {
        return match ($offset) {
            'values' => true,
            default => isset($this->$offset),
        };
    }

    public function &offsetGet(mixed $offset): mixed
    {
        switch ($offset) {
            case 'values':
                return $this->getValues();

            default:
                return $this->$offset;
        }
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->$offset = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->$offset);
    }
}
