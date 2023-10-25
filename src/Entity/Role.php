<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Entity;

class Role extends \Drupal\user\Entity\Role
{
    public function __toString()
    {
        return $this->label;
    }
}
