<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormValidator as CoreFormValidator;

class FormValidator extends CoreFormValidator
{
    /**
     * @param string $form_id
     * @param mixed[] $form
     * @param FormStateInterface $form_state
     */
    public function validateForm($form_id, &$form, FormStateInterface &$form_state): void
    {
        $form_state = new ArrayAccessFormState($form_state);
        parent::validateForm($form_id, $form, $form_state);
    }
}
