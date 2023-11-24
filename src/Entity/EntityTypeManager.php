<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Entity;

use Drupal\Core\Entity\EntityTypeInterface;

class EntityTypeManager extends \Drupal\Core\Entity\EntityTypeManager
{
    /**
     * @param EntityTypeInterface[] $definitions
     */
    protected function alterDefinitions(&$definitions): void
    {
        if (isset($definitions['user_role'])) {
            $definitions['user_role']->setClass(Role::class);
        }
        parent::alterDefinitions($definitions);
    }
}
