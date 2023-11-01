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
    public function getForm(mixed $form_arg): array
    {
        $form_state = new ArrayAccessFormState();
        $args = func_get_args();
        array_shift($args);
        $form_state->addBuildInfo('args', $args);
        return $this->buildForm($form_arg, $form_state);
    }
}
