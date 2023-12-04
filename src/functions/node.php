<?php

declare(strict_types=1);

use Drupal\Core\Entity\EntityInterface;
use Drupal\node\Entity\NodeType;
use Drupal\node\NodeTypeInterface;

function node_load_multiple(array $nids = [], array $conditions = [], bool $reset = false)
{
    return entity_load('node', $nids, $conditions, $reset);
}

function node_load(int|string|null $nid = null, int|string|null $vid = null, bool $reset = false)
{
    $nids = (isset($nid) ? array($nid) : array());
    $conditions = (isset($vid) ? array('vid' => $vid) : array());
    $node = node_load_multiple($nids, $conditions, $reset);
    return $node ? reset($node) : false;
}

function node_view($node, $view_mode = 'full', $langcode = null)
{
    return \Drupal::entityTypeManager()
      ->getViewBuilder('node')
      ->view($node, $view_mode, $langcode);
}

function node_type_get_name(EntityInterface|string $node): string|false
{
    if ($node instanceof EntityInterface) {
        return $node->bundle();
    } else {
        $types = NodeType::loadMultiple();
        return isset($types[$node]) ? $node : false;
    }
}

function node_type_load(string $name): ?NodeTypeInterface
{
    return NodeType::load($name);
}

/**
 * @param array{
 *   type?: string,
 *   name?: string,
 *   base?: string,
 *   description?: string,
 *   help?: string,
 *   custom?: int,
 *   modified?: int,
 *   locked?: int,
 *   disabled?: int,
 *   is_new?: int,
 *   has_title?: int,
 *   title_label?: string
 * } $info
 */
function node_type_set_defaults(array $info = []): NodeTypeInterface
{
    return NodeType::create($info);
}

function node_type_save(NodeTypeInterface $info): int
{
    return $info->save();
}

function node_type_delete(string $type): void
{
    $type = NodeType::load($type);
    assert($type instanceof NodeTypeInterface);
    $type->delete();
}
