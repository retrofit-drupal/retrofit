<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemList as CoreFieldItemList;
use Drupal\field\FieldStorageConfigInterface;
use Symfony\Component\Validator\ConstraintViolation;

final class FieldItemList extends CoreFieldItemList
{
    public function validate()
    {
        $storageDefinition = $this->getFieldDefinition()->getFieldStorageDefinition();
        if ($storageDefinition instanceof FieldStorageConfigInterface) {
            $provider = $storageDefinition->getTypeProvider();
        } else {
            $provider = $storageDefinition->getProvider();
        }
        $violationList =  parent::validate();
        $callable =  $provider . '_field_validate';
        if (is_callable($callable)) {
            $errors = [];
            $callable(
                $this->getEntity()->getEntityTypeId(),
                $this->getEntity(),
                [
                    'field_name' => $this->getName(),
                ],
                [

                ],
                $this->getLangcode(),
                $this,
                $errors
            );
            foreach ($errors[$this->getName()][$this->getLangcode()] ?? [] as $delta => $items) {
                foreach ($items as $error) {
                    $violationList->add(new ConstraintViolation(
                        $error['message'],
                        null,
                        [],
                        $this->getParent(),
                        '',
                        $this->get($delta)?->getValue()
                    ));
                }
            }
        }
        return $violationList;
    }
}
