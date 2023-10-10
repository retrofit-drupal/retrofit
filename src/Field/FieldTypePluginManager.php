<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Field;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FieldTypePluginManager as CoreFieldTypePluginManager;
use Retrofit\Drupal\Plugin\Field\FieldType\DecoratedFieldItem;

final class FieldTypePluginManager extends CoreFieldTypePluginManager
{
    public function createFieldItem(FieldItemListInterface $items, $index, $values = null)
    {
        return new DecoratedFieldItem(parent::createFieldItem($items, $index, $values));
    }
}
