<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Form;

use Drupal\Component\Render\MarkupInterface;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormState;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

final class ArrayAccessFormState implements FormStateInterface, \ArrayAccess
{
    public function __construct(
        protected readonly FormStateInterface $inner
    ) {
    }

    public function offsetExists(mixed $offset): bool
    {
        return match ($offset) {
            'values' => true,
            default => isset($this->inner->$offset),
        };
    }

    public function &offsetGet(mixed $offset): mixed
    {
        switch ($offset) {
            case 'values':
                return $this->inner->getValues();

            default:
                return $this->inner->$offset;
        }
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->inner->$offset = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->inner->$offset);
    }

    public function __set(string $name, mixed $value): void
    {
        $this->inner->$name = $value;
    }

    public function __get(string $name): mixed
    {
        return $this->inner->$name;
    }

    public function __isset(string $name): bool
    {
        return isset($this->inner->$name);
    }

    public function __unset(string $name): void
    {
        unset($this->inner->$name);
    }

    /**
     * @param mixed[] $arguments
     */
    public function __call(string $name, array $arguments): mixed
    {
        return $this->inner->$name($arguments);
    }

    /**
     * @param mixed[] $arguments
     */
    public static function __callStatic(string $name, array $arguments): mixed
    {
        return FormState::$name($arguments);
    }

    /**
     * @return mixed[]
     */
    public function &getCompleteForm(): array
    {
        return $this->inner->getCompleteForm();
    }

    /**
     * @param mixed[] $complete_form
     */
    public function setCompleteForm(array &$complete_form): static
    {
        $this->inner->setCompleteForm($complete_form);
        return $this;
    }

    /**
     * @param string $module
     * @param string $type
     * @param ?string $name
     */
    public function loadInclude($module, $type, $name = null): string|false
    {
        return $this->inner->loadInclude($module, $type, $name);
    }

    /**
     * @return mixed[]
     */
    public function getCacheableArray(): array
    {
        return $this->inner->getCacheableArray();
    }

    /**
     * @param mixed[] $form_state_additions
     */
    public function setFormState(array $form_state_additions): static
    {
        $this->inner->setFormState($form_state_additions);
        return $this;
    }

    public function setResponse(Response $response): static
    {
        $this->inner->setResponse($response);
        return $this;
    }

    public function getResponse(): ?Response
    {
        return $this->inner->getResponse();
    }

    /**
     * @param string $route_name
     * @param mixed[] $route_parameters
     * @param mixed[] $options
     */
    public function setRedirect($route_name, array $route_parameters = [], array $options = []): static
    {
        $this->inner->setRedirect($route_name, $route_parameters, $options);
        return $this;
    }

    public function setRedirectUrl(Url $url): static
    {
        $this->inner->setRedirectUrl($url);
        return $this;
    }

    public function getRedirect(): mixed
    {
        return $this->inner->getRedirect();
    }

    /**
     * @param mixed[] $storage
     */
    public function setStorage(array $storage): static
    {
        $this->inner->setStorage($storage);
        return $this;
    }

    /**
     * @return mixed[]
     */
    public function &getStorage(): array
    {
        return $this->inner->getStorage();
    }

    /**
     * @param string|array<string, int> $property
     */
    public function &get($property): mixed
    {
        return $this->inner->get($property);
    }

    /**
     * @param string|array<string, int> $property
     */
    public function set($property, mixed $value): static
    {
        $this->inner->set($property, $value);
        return $this;
    }

    /**
     * @param string|array<string, int> $property
     */
    public function has($property): ?bool
    {
        return $this->inner->has($property);
    }

    /**
     * @param mixed[] $build_info
     */
    public function setBuildInfo(array $build_info): static
    {
        $this->inner->setBuildInfo($build_info);
        return $this;
    }

    /**
     * @return mixed[]
     */
    public function getBuildInfo(): array
    {
        return $this->inner->getBuildInfo();
    }

    public function addBuildInfo($property, mixed $value): static
    {
        $this->inner->addBuildInfo($property, $value);
        return $this;
    }

    /**
     * @return mixed[]
     */
    public function &getUserInput(): ?array
    {
        return $this->inner->getUserInput();
    }

    /**
     * @param mixed[] $user_input
     */
    public function setUserInput(array $user_input): static
    {
        $this->inner->setUserInput($user_input);
        return $this;
    }

    /**
     * @return mixed[]
     */
    public function &getValues(): array
    {
        return $this->inner->getValues();
    }

    /**
     * @param string|array<string, int> $key
     * @param mixed $default
     */
    public function &getValue($key, $default = null): mixed
    {
        return $this->inner->getValue($key, $default);
    }

    /**
     * @param mixed[] $values
     */
    public function setValues(array $values): static
    {
        $this->inner->setValues($values);
        return $this;
    }

    /**
     * @param string|array<string, int> $key
     */
    public function setValue($key, mixed $value): static
    {
        $this->inner->setValue($key, $value);
        return $this;
    }

    /**
     * @param string|array<string, int> $key
     */
    public function unsetValue($key): static
    {
        $this->inner->unsetValue($key);
        return $this;
    }

    /**
     * @param string|array<string, int> $key
     */
    public function hasValue($key): bool
    {
        return $this->inner->hasValue($key);
    }

    /**
     * @param string|array<string, int> $key
     */
    public function isValueEmpty($key): bool
    {
        return $this->inner->isValueEmpty($key);
    }

    /**
     * @param mixed[] $element
     */
    public function setValueForElement(array $element, mixed $value): static
    {
        $this->inner->setValueForElement($element, $value);
        return $this;
    }

    public static function hasAnyErrors(): bool
    {
        return FormState::hasAnyErrors();
    }

    public function setErrorByName($name, $message = ''): static
    {
        $this->inner->setErrorByName($name, $message);
        return $this;
    }

    /**
     * @param mixed[] $element
     */
    public function setError(array &$element, $message = ''): static
    {
        $this->inner->setError($element, $message);
        return $this;
    }

    public function clearErrors(): void
    {
        $this->inner->clearErrors();
    }

    /**
     * @return string[]
     */
    public function getErrors(): array
    {
        return $this->inner->getErrors();
    }

    /**
     * @param mixed[] $element
     */
    public function getError(array $element): string|MarkupInterface|null
    {
        return $this->inner->getError($element);
    }

    public function setRebuild($rebuild = true): static
    {
        $this->inner->setRebuild($rebuild);
        return $this;
    }

    public function isRebuilding(): bool
    {
        return $this->inner->isRebuilding();
    }

    public function setInvalidToken($invalid_token): static
    {
        $this->inner->setInvalidToken($invalid_token);
        return $this;
    }

    public function hasInvalidToken(): bool
    {
        return $this->inner->hasInvalidToken();
    }

    /**
     * @param string[]|string $callback
     * @return callable|string|string[]
     */
    public function prepareCallback($callback): callable|string|array
    {
        return $this->inner->prepareCallback($callback);
    }

    public function getFormObject(): FormInterface
    {
        return $this->inner->getFormObject();
    }

    public function setFormObject(FormInterface $form_object): static
    {
        $this->inner->setFormObject($form_object);
        return $this;
    }

    public function setAlwaysProcess($always_process = true): static
    {
        $this->inner->setAlwaysProcess($always_process);
        return $this;
    }

    public function getAlwaysProcess(): ?bool
    {
        return $this->inner->getAlwaysProcess();
    }

    /**
     * @param mixed[] $buttons
     */
    public function setButtons(array $buttons): static
    {
        $this->inner->setButtons($buttons);
        return $this;
    }

    /**
     * @return mixed[]
     */
    public function getButtons(): array
    {
        return $this->inner->getButtons();
    }

    public function setCached($cache = true): static
    {
        $this->inner->setCached($cache);
        return $this;
    }

    public function isCached(): bool
    {
        return $this->inner->isCached();
    }

    public function disableCache(): static
    {
        $this->inner->disableCache();
        return $this;
    }

    public function setExecuted(): static
    {
        $this->inner->setExecuted();
        return $this;
    }

    public function isExecuted(): bool
    {
        return $this->inner->isExecuted();
    }

    /**
     * @param mixed[] $groups
     */
    public function setGroups(array $groups): static
    {
        $this->inner->setGroups($groups);
        return $this;
    }

    /**
     * @return mixed[]
     */
    public function &getGroups(): array
    {
        return $this->inner->getGroups();
    }

    public function setHasFileElement($has_file_element = true): static
    {
        $this->inner->setHasFileElement($has_file_element);
        return $this;
    }

    public function hasFileElement(): ?bool
    {
        return $this->inner->hasFileElement();
    }

    /**
     * @param ?array<string, int> $limit_validation_errors
     */
    public function setLimitValidationErrors($limit_validation_errors): static
    {
        $this->inner->setLimitValidationErrors($limit_validation_errors);
        return $this;
    }

    /**
     * @return ?array<string, int>
     */
    public function getLimitValidationErrors(): ?array
    {
        return $this->inner->getLimitValidationErrors();
    }

    public function setMethod($method): static
    {
        $this->inner->setMethod($method);
        return $this;
    }

    public function setRequestMethod($method): static
    {
        $this->inner->setRequestMethod($method);
        return $this;
    }

    public function isMethodType($method_type): bool
    {
        return $this->inner->isMethodType($method_type);
    }

    public function setValidationEnforced($must_validate = true): static
    {
        $this->inner->setValidationEnforced($must_validate);
        return $this;
    }

    public function isValidationEnforced(): ?bool
    {
        return $this->inner->isValidationEnforced();
    }

    public function disableRedirect($no_redirect = true): static
    {
        $this->inner->disableRedirect($no_redirect);
        return $this;
    }

    public function isRedirectDisabled(): bool
    {
        return $this->inner->isRedirectDisabled();
    }

    public function setProcessInput($process_input = true): static
    {
        $this->inner->setProcessInput($process_input);
        return $this;
    }

    public function isProcessingInput(): bool
    {
        return $this->inner->isProcessingInput();
    }

    public function setProgrammed($programmed = true): static
    {
        $this->inner->setProgrammed($programmed);
        return $this;
    }

    public function isProgrammed(): bool
    {
        return $this->inner->isProgrammed();
    }

    public function setProgrammedBypassAccessCheck($programmed_bypass_access_check = true): static
    {
        $this->inner->setProgrammedBypassAccessCheck($programmed_bypass_access_check);
        return $this;
    }

    public function isBypassingProgrammedAccessChecks(): bool
    {
        return $this->inner->isBypassingProgrammedAccessChecks();
    }

    /**
     * @param mixed[] $rebuild_info
     */
    public function setRebuildInfo(array $rebuild_info): static
    {
        $this->inner->setRebuildInfo($rebuild_info);
        return $this;
    }

    /**
     * @return mixed[]
     */
    public function getRebuildInfo(): array
    {
        return $this->inner->getRebuildInfo();
    }

    public function addRebuildInfo($property, mixed $value): static
    {
        $this->inner->addRebuildInfo($property, $value);
        return $this;
    }

    /**
     * @param array<callable, string> $submit_handlers
     */
    public function setSubmitHandlers(array $submit_handlers): static
    {
        $this->inner->setSubmitHandlers($submit_handlers);
        return $this;
    }

    /**
     * @return array<callable, string>
     */
    public function getSubmitHandlers(): array
    {
        return $this->inner->getSubmitHandlers();
    }

    public function setSubmitted(): static
    {
        $this->inner->setSubmitted();
        return $this;
    }

    public function isSubmitted(): bool
    {
        return $this->inner->isSubmitted();
    }

    /**
     * @param mixed[] $temporary
     */
    public function setTemporary(array $temporary): static
    {
        $this->inner->setTemporary($temporary);
        return $this;
    }

    /**
     * @return mixed[]
     */
    public function getTemporary(): array
    {
        return $this->inner->getTemporary();
    }

    /**
     * @param string|array<string, int> $key
     */
    public function &getTemporaryValue($key): mixed
    {
        return $this->inner->getTemporaryValue($key);
    }

    /**
     * @param string|array<string, int> $key
     */
    public function setTemporaryValue($key, mixed $value): static
    {
        $this->inner->setTemporaryValue($key, $value);
        return $this;
    }

    public function hasTemporaryValue($key): ?bool
    {
        return $this->inner->hasTemporaryValue($key);
    }

    /**
     * @param mixed[] $triggering_element
     */
    public function setTriggeringElement($triggering_element): static
    {
        $this->inner->setTriggeringElement($triggering_element);
        return $this;
    }

    /**
     * @return ?mixed[]
     */
    public function &getTriggeringElement(): ?array
    {
        return $this->inner->getTriggeringElement();
    }

    /**
     * @param array<callable, string> $validate_handlers
     */
    public function setValidateHandlers(array $validate_handlers): static
    {
        $this->inner->setValidateHandlers($validate_handlers);
        return $this;
    }

    /**
     * @return array<callable, string>
     */
    public function getValidateHandlers(): array
    {
        return $this->inner->getValidateHandlers();
    }

    public function setValidationComplete($validation_complete = true): static
    {
        $this->inner->setValidationComplete($validation_complete);
        return $this;
    }

    public function isValidationComplete(): bool
    {
        return $this->inner->isValidationComplete();
    }

    /**
     * @return array<string, int>
     */
    public function getCleanValueKeys(): array
    {
        return $this->inner->getCleanValueKeys();
    }

    /**
     * @param array<string, int> $keys
     */
    public function setCleanValueKeys(array $keys): static
    {
        $this->inner->setCleanValueKeys($keys);
        return $this;
    }

    public function addCleanValueKey($key): static
    {
        $this->inner->addCleanValueKey($key);
        return $this;
    }

    public function cleanValues(): static
    {
        $this->inner->cleanValues();
        return $this;
    }
}
