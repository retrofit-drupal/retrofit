<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Form;

use Drupal\Core\Form\FormBuilder as CoreFormBuilder;
use Drupal\Core\Form\FormStateInterface;
use Retrofit\Drupal\Form\ArrayAccessFormState;

class FormBuilder extends CoreFormBuilder
{
    /**
     * @param FormStateInterface|mixed[] $form_state
     * @return mixed[]
     */
    public function buildForm(mixed $form_arg, FormStateInterface|array &$form_state): array
    {
        $original_form_state = $form_state;
        $form_state = new ArrayAccessFormState();
        if ($original_form_state instanceof FormStateInterface) {
            $original_form_state = $original_form_state->getCacheableArray() + get_object_vars($original_form_state);
        }
        foreach ($original_form_state as $offset => $value) {
            $form_state[$offset] = $value;
        }
        return parent::buildForm($form_arg, $form_state);
    }
}
