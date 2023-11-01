<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Field;

use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FieldTypePluginManager as CoreFieldTypePluginManager;
use Drupal\Core\Field\Plugin\Field\FieldType\EntityReferenceItem;
use Retrofit\Drupal\Plugin\Field\FieldType\DecoratedFieldItem;

final class FieldTypePluginManager extends CoreFieldTypePluginManager
{
    /**
     * @param FieldItemListInterface<FieldItemInterface> $items
     * @param int $index
     * @param array<int, mixed> $values
     * @return FieldItemInterface<string|int, mixed>
     */
    public function createFieldItem(FieldItemListInterface $items, $index, $values = null): FieldItemInterface
    {
        $item = parent::createFieldItem($items, $index, $values);
        // @todo Find fix that allows all field types to be accessed as arrays without breaking typed arguments.
        if (!($item instanceof EntityReferenceItem)) {
            $item = new DecoratedFieldItem($item);
        }
        return $item;
    }
}
