<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

final class DrupalGetForm extends FormBase
{
    protected string $formId;

    public function setFormId(string $formId): void
    {
        $this->formId = $formId;
    }

    public function getFormId(): string
    {
        return $this->formId;
    }

    public function buildForm(array $form, FormStateInterface $form_state, ...$args): array
    {
        return ($this->formId)($form, $form_state, ...$args);
    }

    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        $callback = $this->formId . '_validate';
        if (is_callable($callback)) {
            $callback($form, $form_state);
            $errors = form_get_errors() ?? [];
            foreach ($errors as $element => $message) {
                $form_state->setErrorByName($element, $message);
            }
        }
    }

    public function submitForm(array &$form, FormStateInterface $form_state): void
    {
        $callback = $this->formId . '_submit';
        if (is_callable($callback)) {
            $callback($form, $form_state);
        }
    }
}
