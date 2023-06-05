<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * @FieldFormatter(
 *   id = "retrofit_field_formatter",
 *   deriver = "\Retrofit\Drupal\Plugin\Derivative\FieldFormatterDeriver",
 * )
 */
final class FieldFormatter extends FormatterBase
{
    public function viewElements(FieldItemListInterface $items, $langcode)
    {
        $element = [];
        $callable = $this->pluginDefinition['provider'] . '_field_formatter_view';
        if (is_callable($callable)) {
            $element = $callable(
                $items->getEntity()->getEntityTypeId(),
                $items->getEntity(),
                $items->getFieldDefinition(),
                $items->getFieldDefinition()->getFieldStorageDefinition(),
                $items->getLangcode(),
                $items,
                [
                'type' => $this->getDerivativeId(),
                'settings' => $this->configuration,
                ]
            );
        }
        return $element;
    }
}
