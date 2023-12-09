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
        if ($form_state instanceof FormStateInterface) {
            $original_form_state = $form_state->getCacheableArray() + get_object_vars($form_state) + [
                'always_process' => $form_state->getAlwaysProcess(),
                'buttons' => $form_state->getButtons(),
                'cleanValueKeys' => $form_state->getCleanValueKeys(),
                'complete_form' => $form_state->getCompleteForm(),
                'errors' => $form_state->getErrors(),
                'executed' => $form_state->isExecuted(),
                'groups' => $form_state->getGroups(),
                'input' => $form_state->getUserInput(),
                'invalidToken' => $form_state->hasInvalidToken(),
                'limit_validation_errors' => $form_state->getLimitValidationErrors(),
                'method' => match (true) {
                    $form_state->isMethodType('POST') => 'POST',
                    $form_state->isMethodType('GET') => 'GET',
                    $form_state->isMethodType('HEAD') => 'HEAD',
                    $form_state->isMethodType('OPTIONS') => 'OPTIONS',
                    $form_state->isMethodType('PUT') => 'PUT',
                    $form_state->isMethodType('DELETE') => 'DELETE',
                    $form_state->isMethodType('TRACE') => 'TRACE',
                    $form_state->isMethodType('CONNECT') => 'CONNECT',
                    $form_state->isMethodType('PATCH') => 'PATCH',
                    default => 'POST',
                },
                'must_validate' => $form_state->isValidationEnforced(),
                'no_redirect' => $form_state->isRedirectDisabled(),
                'rebuild' => $form_state->isRebuilding(),
                'rebuild_info' => $form_state->getRebuildInfo(),
                'redirect' => $form_state->getRedirect(),
                'submitted' => $form_state->isSubmitted(),
                'submit_handlers' => $form_state->getSubmitHandlers(),
                'temporary' => $form_state->getTemporary(),
                'triggering_element' => $form_state->getTriggeringElement(),
                'validate_handlers' => $form_state->getValidateHandlers(),
                'validation_complete' => $form_state->isValidationComplete(),
                'values' => $form_state->getValues(),
            ];
            $form_state = new ArrayAccessFormState(call_user_func([get_class($form_state), 'hasAnyErrors']));
        } else {
            $original_form_state = $form_state;
            $form_state = new ArrayAccessFormState();
        }
        foreach ($original_form_state as $offset => $value) {
            $form_state[$offset] = $value;
        }
        return parent::buildForm($form_arg, $form_state);
    }
}
