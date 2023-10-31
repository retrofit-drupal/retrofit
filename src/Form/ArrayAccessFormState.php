<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Form;

use Drupal\Component\Render\MarkupInterface;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormState;
use Drupal\Core\Form\FormStateDecoratorBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\GeneratedUrl;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

final class ArrayAccessFormState extends FormStateDecoratorBase implements \ArrayAccess
{
    public function __construct(FormStateInterface $decoratedFormState)
    {
        $this->decoratedFormState = $decoratedFormState;
    }

    public function offsetExists(mixed $offset): bool
    {
        return match ($offset) {
            'always_process',
            'build_info',
            'buttons',
            'cache',
            'complete_form',
            'executed',
            'groups',
            'has_file_element',
            'input',
            'method',
            'must_validate',
            'no_cache',
            'no_redirect',
            'process_input',
            'programmed',
            'programmed_bypass_access_check',
            'rebuild',
            'rebuild_info',
            'redirect',
            'storage',
            'submitted',
            'temporary',
            'clicked_button',
            'triggering_element',
            'values' => true,
            default => isset($this->decoratedFormState->$offset),
        };
    }

    public function &offsetGet(mixed $offset): mixed
    {
        switch ($offset) {
            case 'always_process':
                return $this->getAlwaysProcess();
            case 'build_info':
                return $this->getBuildInfo();
            case 'buttons':
                return $this->getButtons();
            case 'cache':
                return $this->isCached();
            case 'complete_form':
                return $this->getCompleteForm();
            case 'executed':
                return $this->isExecuted();
            case 'groups':
                return $this->getGroups();
            case 'has_file_element':
                return $this->hasFileElement();
            case 'input':
                return $this->getUserInput();
            case 'method':
                return $this->isMethodType('POST') ? 'post' : 'get';
            case 'must_validate':
                return $this->isValidationEnforced();
            case 'no_cache':
                return $this->isCached();
            case 'no_redirect':
                return $this->isRedirectDisabled();
            case 'process_input':
                return $this->isProcessingInput();
            case 'programmed':
                return $this->isProgrammed();
            case 'programmed_bypass_access_check':
                return $this->isBypassingProgrammedAccessChecks();
            case 'rebuild':
                return $this->isRebuilding();
            case 'rebuild_info':
                return $this->getRebuildInfo();
            case 'redirect':
                return $this->getRedirect();
            case 'storage':
                return $this->getStorage();
            case 'submitted':
                return $this->isSubmitted();
            case 'temporary':
                return $this->getTemporary();
            case 'clicked_button':
            case 'triggering_element':
                return $this->getTriggeringElement();
            case 'values':
                return $this->getValues();
            default:
                return $this->decoratedFormState->$offset;
        }
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        switch ($offset) {
            case 'always_process':
                $this->setAlwaysProcess((bool) $value);
                break;
            case 'build_info':
                $this->setBuildInfo((array) $value);
                break;
            case 'buttons':
                $this->setButtons((array) $value);
                break;
            case 'cache':
                $this->setCached((bool) $value);
                break;
            case 'complete_form':
                $this->setCompleteForm($value);
                break;
            case 'always_process':
                $this->setAlwaysProcess((bool) $value);
                break;
            case 'groups':
                $this->setGroups((array) $value);
                break;
            case 'has_file_element':
                $this->setHasFileElement((bool) $value);
                break;
            case 'input':
                $this->setUserInput((array) $value);
                break;
            case 'method':
                if (is_string($value)) {
                    $this->setMethod($value);
                }
                break;
            case 'must_validate':
                $this->setValidationEnforced((bool) $value);
                break;
            case 'no_redirect':
                $this->disableRedirect((bool) $value);
                break;
            case 'process_input':
                $this->setProcessInput((bool) $value);
                break;
            case 'programmed':
                $this->setProgrammed((bool) $value);
                break;
            case 'programmed_bypass_access_check':
                $this->setProgrammedBypassAccessCheck((bool) $value);
                break;
            case 'rebuild':
                $this->setRebuild((bool) $value);
                break;
            case 'rebuild_info':
                $this->setRebuildInfo((array) $value);
                break;
            case 'redirect':
                if (is_string($value) && $url = \Drupal::pathValidator()->getUrlIfValidWithoutAccessCheck($value)) {
                    $this->setRedirectUrl($url);
                } elseif (is_array($value)) {
                    $path = array_shift($value);
                    if (is_string($path) && $url = \Drupal::pathValidator()->getUrlIfValidWithoutAccessCheck($path)) {
                        $options = array_shift($value) ?: [];
                        $url->mergeOptions((array) $options);
                        $this->setRedirectUrl($url);
                    }
                }
                break;
            case 'storage':
                $this->setStorage((array) $value);
                break;
            case 'temporary':
                $this->setTemporary((array) $value);
                break;
            case 'clicked_button':
            case 'triggering_element':
                $this->setTriggeringElement(isset($value) ? (array) $value : null);
                break;
            default:
                $this->decoratedFormState->$offset = $value;
        }
    }

    public function offsetUnset(mixed $offset): void
    {
        switch ($offset) {
            case 'always_process':
                $this->setAlwaysProcess(false);
                break;
            case 'build_info':
                $this->setBuildInfo([
                    'args' => [],
                    'files' => [],
                ]);
                break;
            case 'buttons':
                $this->setButtons([]);
                break;
            case 'cache':
                $this->setCached(false);
                break;
            case 'complete_form':
                $form = [];
                $this->setCompleteForm($form);
                break;
            case 'always_process':
                $this->setAlwaysProcess(false);
                break;
            case 'groups':
                $this->setGroups([]);
                break;
            case 'has_file_element':
                $this->setHasFileElement(false);
                break;
            case 'input':
                $this->setUserInput([]);
                break;
            case 'method':
                $this->setMethod('POST');
                break;
            case 'must_validate':
                $this->setValidationEnforced(false);
                break;
            case 'no_redirect':
                $this->disableRedirect(false);
                break;
            case 'process_input':
                $this->setProcessInput(false);
                break;
            case 'programmed':
                $this->setProgrammed(false);
                break;
            case 'programmed_bypass_access_check':
                $this->setProgrammedBypassAccessCheck();
                break;
            case 'rebuild':
                $this->setRebuild(false);
                break;
            case 'rebuild_info':
                $this->setRebuildInfo([]);
                break;
            case 'redirect':
                $this->setRedirectUrl(Url::fromRoute('<none>'));
                break;
            case 'storage':
                $this->setStorage([]);
                break;
            case 'temporary':
                $this->setTemporary([]);
                break;
            case 'clicked_button':
            case 'triggering_element':
                $this->setTriggeringElement(null);
                break;
            default:
                unset($this->decoratedFormState->$offset);
        }
    }
}
