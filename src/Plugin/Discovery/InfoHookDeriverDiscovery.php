<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Plugin\Discovery;

use Drupal\Component\Plugin\Discovery\DiscoveryInterface;

final class InfoHookDeriverDiscovery implements DiscoveryInterface
{
    /**
     * @param  class-string  $deriver
     */
    public function __construct(
        private readonly DiscoveryInterface $decorated,
        private readonly string $baseId,
        private readonly string $deriver
    ) {
    }

    public function getDefinitions()
    {
        $definitions = $this->decorated->getDefinitions();
        $definitions[$this->baseId] = [
          'id' => $this->baseId,
          'deriver' => $this->deriver,
        ];
        return $definitions;
    }


    public function getDefinition($plugin_id, $exception_on_invalid = true)
    {
        return $this->decorated->getDefinition($plugin_id, $exception_on_invalid);
    }

    public function hasDefinition($plugin_id)
    {
        return $this->decorated->hasDefinition($plugin_id);
    }
}
