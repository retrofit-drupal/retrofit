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
            $field_storage_definition = $this->fieldDefinition->getFieldStorageDefinition();
            $instance = $this->fieldDefinition->toArray();
            $instance['default_value_function'] = $instance['default_value_callback'];
            if ($instance['default_value'] === []) {
                $info = $this->pluginDefinition['field_widget_info'];
                if (($info['behaviors']['default value'] ?? FIELD_BEHAVIOR_DEFAULT) == FIELD_BEHAVIOR_DEFAULT) {
                    $instance['default_value'] = [null];
                }
            }
            $instance['widget'] = [
                'type' => $this->pluginDefinition['widget'],
                'settings' => $this->pluginDefinition['field_widget_info']['settings'],
                'weight' => $this->pluginDefinition['weight'],
                'module' => $this->pluginDefinition['provider'],
            ];
            $columns = FieldItem::schema($field_storage_definition)['columns'];
            $element += [
                '#entity_type' => $this->fieldDefinition->getTargetEntityTypeId(),
                '#bundle' => $this->fieldDefinition->getTargetBundle(),
                '#field_name' => $items->getName(),
                '#language' => $items->getLangcode(),
                '#columns' => $columns,
            ];
            $items_array = [];
            foreach ($items as $position => $item) {
                $items_array[$position] = $item->toArray();
            }
            return $callable(
                $form,
                $form_state,
                $field_storage_definition->toArray() + ['columns' => $columns],
                $instance,
                $items->getLangcode(),
                $items_array,
                $delta,
                $element
            );
        }
        return ['value' => $element];
    }
}
