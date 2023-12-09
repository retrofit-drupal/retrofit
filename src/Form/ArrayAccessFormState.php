<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Form;

use Drupal\Core\Form\FormState;
use Drupal\Core\Url;

final class ArrayAccessFormState extends FormState implements \ArrayAccess
{
    public function __construct(bool $errors = false)
    {
        static::setAnyErrors($errors);
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->$offset);
    }

    public function &offsetGet(mixed $offset): mixed
    {
        switch ($offset) {
            case 'complete_form':
                return $this->getCompleteForm();
            case 'groups':
                return $this->getGroups();
            case 'input':
                return $this->getUserInput();
            case 'storage':
                return $this->getStorage();
            case 'clicked_button':
            case 'triggering_element':
                return $this->getTriggeringElement();
            case 'values':
                return $this->getValues();
            default:
                return $this->$offset;
        }
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        switch ($offset) {
            case 'redirect':
                if (is_string($value) && $url = \Drupal::pathValidator()->getUrlIfValidWithoutAccessCheck($value)) {
                    $this->redirect = $url;
                } elseif (is_array($value)) {
                    $path = array_shift($value);
                    if (is_string($path) && $url = \Drupal::pathValidator()->getUrlIfValidWithoutAccessCheck($path)) {
                        $options = array_shift($value) ?: [];
                        $url->mergeOptions((array) $options);
                        $this->redirect = $url;
                    }
                }
                break;
            default:
                $this->$offset = $value;
        }
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->$offset);
    }
}
