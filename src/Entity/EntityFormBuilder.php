<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Entity;

use Drupal\Core\Entity\EntityFormBuilder as CoreEntityFormBuilder;
use Drupal\Core\Entity\EntityInterface;
use Retrofit\Drupal\Form\ArrayAccessFormState;

class EntityFormBuilder extends CoreEntityFormBuilder
{
    /**
     * @param string $operation
     * @param mixed[] $form_state_additions
     * @return mixed[]
     */
    public function getForm(EntityInterface $entity, $operation = 'default', array $form_state_additions = []): array
    {
        $form_object = $this->entityTypeManager->getFormObject($entity->getEntityTypeId(), $operation);
        $form_object->setEntity($entity);
        $form_state = (new ArrayAccessFormState())->setFormState($form_state_additions);
        return $this->formBuilder->buildForm($form_object, $form_state);
    }
}
