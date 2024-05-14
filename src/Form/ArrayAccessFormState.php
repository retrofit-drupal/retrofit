<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Form;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Form\FormState;
use Drupal\Core\Url;

#[\AllowDynamicProperties]
final class ArrayAccessFormState extends FormState implements \ArrayAccess
{
    public function __construct(bool $errors = false)
    {
        static::setAnyErrors($errors);
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->has($offset);
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
                return $this->get($offset);
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
            case 'rebuild':
                $this->setRebuild((bool) $value);
                break;
            default:
                $this->set($offset, $value);
        }
    }

    public function offsetUnset(mixed $offset): void
    {
        NestedArray::unsetValue($this->storage, (array) $offset);
    }
}
