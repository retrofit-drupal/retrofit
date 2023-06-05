<?php

declare(strict_types=1);

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

function entity_type_manager(): EntityTypeManagerInterface
{
    return \Drupal::entityTypeManager();
}

function entity_type_storage(string $entity_type_id): EntityStorageInterface
{
    return entity_type_manager()->getStorage($entity_type_id);
}
