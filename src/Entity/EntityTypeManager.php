<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Entity;

use Drupal\Core\Entity\EntityTypeInterface;

class EntityTypeManager extends \Drupal\Core\Entity\EntityTypeManager
{
    /**
     * @return EntityTypeInterface[]
     */
    protected function findDefinitions(): array
    {
        $definitions = $this->getDiscovery()->getDefinitions();
        if (isset($definitions['user_role']) && $definitions['user_role'] instanceof EntityTypeInterface) {
            $definitions['user_role']->setClass(Role::class);
        }
        $this->moduleHandler->invokeAllWith('entity_type_build', function (
            callable $hook,
            string $module
        ) use (&$definitions) {
            $hook($definitions);
        });
        foreach ($definitions as $plugin_id => $definition) {
            $this->processDefinition($definition, $plugin_id);
        }
        $this->alterDefinitions($definitions);

        return $definitions;
    }
}
