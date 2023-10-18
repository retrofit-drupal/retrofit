<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Field;

use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FieldTypePluginManager as CoreFieldTypePluginManager;
use Retrofit\Drupal\Plugin\Field\FieldType\DecoratedFieldItem;

final class FieldTypePluginManager extends CoreFieldTypePluginManager
{
    /**
     * @param FieldItemListInterface<FieldItemInterface> $items
     * @param int $index
     * @param array<int, mixed> $values
     * @return DecoratedFieldItem
     */
    public function createFieldItem(FieldItemListInterface $items, $index, $values = null): DecoratedFieldItem
    {
        return new DecoratedFieldItem(parent::createFieldItem($items, $index, $values));
    }
}
