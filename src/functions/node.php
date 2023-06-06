<?php

declare(strict_types=1);

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
      ->getViewBuilder('nod')
      ->view($node, $view_mode, $langcode);
}
