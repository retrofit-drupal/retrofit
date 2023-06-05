<?php

declare(strict_types=1);

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Language\LanguageInterface;

/**
 * @todo flush out
 * this cannot call Url objects because they may generate routes and could
 * cause a recurvise router rebuild. Copy the original code from D7.
 *
 * @link https://git.drupalcode.org/project/drupal/-/blob/7.x/includes/common.inc#L2300
 */
function url(?string $path = null, array $options = array()): string
{
    if ($path === null) {
        return '/';
    }

    if ($path[0] !== '/') {
        $path = "/$path";
    }
    return $path;
}

function entity_load(string $entity_type, array|false $ids = false, array $conditions = [], bool $reset = false)
{
    $controller = entity_get_controller($entity_type);
    if ($reset) {
        $controller->resetCache();
    }
    if ($conditions === []) {
        return $controller->loadMultiple($ids);
    }
    return $controller->loadByProperties($conditions);
}

function entity_load_unchanged(string $entity_type, int|string $id)
{
    return entity_get_controller($entity_type)->loadUnchanged($id);
}

function entity_get_controller(string $entity_type)
{
    // @todo should this return the storage or a decorated storage?
    return \Drupal::entityTypeManager()->getStorage($entity_type);
}

function entity_uri(string $entity_type, EntityInterface $entity)
{
    return $entity->toUrl()->toString();
}

function entity_extract_ids(string $entity_type, EntityInterface $entity)
{
    return [
      $entity->id(),
      $entity instanceof RevisionableInterface ? $entity->getRevisionId() : null,
      $entity->bundle()
    ];
}

function entity_language(string $entity_type, EntityInterface $entity)
{
    $langcode = $entity->language()->getId();
    return $langcode === LanguageInterface::LANGCODE_NOT_SPECIFIED ? null : $langcode;
}
