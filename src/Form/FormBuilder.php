<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Form;

use Drupal\Core\Form\FormBuilder as CoreFormBuilder;
use Drupal\Core\Form\FormStateInterface;
use Retrofit\Drupal\Form\ArrayAccessFormState;

class FormBuilder extends CoreFormBuilder
{
    /**
     * @return mixed[]
     */
    public function buildForm(mixed $form_arg, FormStateInterface &$form_state): array
    {
        if (!($form_state instanceof ArrayAccessFormState)) {
            $form_state = new ArrayAccessFormState($form_state);
        }
        return parent::buildForm($form_arg, $form_state);
    }
}
