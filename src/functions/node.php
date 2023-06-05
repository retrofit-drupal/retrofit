<?php

declare(strict_types=1);

use Drupal\node\Entity\Node;

function node_load($nid, $vid = null, $reset = false)
{
    return Node::load($nid);
}

function node_view($node, $view_mode = 'full', $langcode = null)
{
    return \Drupal::entityTypeManager()
      ->getViewBuilder('nod')
      ->view($node, $view_mode, $langcode);
}
