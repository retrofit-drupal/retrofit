<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Retrofit\Drupal\Plugin\Field\FieldType\FieldItem;

/**
 * Plugin implementation of the 'retrofit_widget' widget.
 *
 * @FieldWidget(
 *     id = "retrofit_field_widget",
 *     deriver = "\Retrofit\Drupal\Plugin\Derivative\FieldWidgetDeriver"
 * )
 */
final class FieldWidget extends WidgetBase
{
    public static function defaultSettings()
    {
        return parent::defaultSettings();
    }

    public function settingsForm(array $form, FormStateInterface $form_state)
    {
        return [];
    }

    public function settingsSummary()
    {
        return [];
    }

    public function formElement(
        FieldItemListInterface $items,
        $delta,
        array $element,
        array &$form,
        FormStateInterface $form_state
    ): array {
        $callable = $this->pluginDefinition['provider'] . '_field_widget_form';
        if (is_callable($callable)) {
            $instance = $items->getFieldDefinition();
            $field_definition = $instance->getFieldStorageDefinition();
            $columns = FieldItem::schema($field_definition)['columns'];
            $element += [
                '#entity_type' => $instance->getTargetEntityTypeId(),
                '#bundle' => $instance->getTargetBundle(),
                '#field_name' => $items->getName(),
                '#language' => $items->getLangcode(),
                '#columns' => $columns,
            ];
            $state_array = $form_state->getCacheableArray();
            $items_array = [];
            foreach ($items as $position => $item) {
                $items_array[$position] = $item->toArray();
            }
            return $callable(
                $form,
                $state_array,
                $field_definition->toArray() + ['columns' => $columns],
                $instance->toArray(),
                $items->getLangcode(),
                $items_array,
                $delta,
                $element
            );
        }
        return ['value' => $element];
    }
}
