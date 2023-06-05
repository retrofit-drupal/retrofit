<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * @Block(
 *   id = "retrofit_block",
 *   deriver = "\Retrofit\Drupal\Plugin\Derivative\BlockDeriver",
 * )
 */
final class Block extends BlockBase
{
    public function build()
    {
        $callable = $this->pluginDefinition['provider'] . '_block_view';
        if (!is_callable($callable)) {
            return [];
        }
        $result = $callable($this->getDerivativeId());
        return $result['content'];
    }

    public function blockForm($form, FormStateInterface $form_state)
    {
        $callable = $this->pluginDefinition['provider'] . '_block_configure';
        if (is_callable($callable)) {
            $result = $callable($this->getDerivativeId());
            $form += $result;
        }
        return $form;
    }

    public function blockSubmit($form, FormStateInterface $form_state)
    {
        $callable = $this->pluginDefinition['provider'] . '_block_save';
        if (is_callable($callable)) {
            $callable($this->getDerivativeId(), $form_state->getValues());
        }
    }


    public function getCacheContexts()
    {
        $cache_contexts = parent::getCacheContexts();
        $cache = $this->pluginDefinition['block_info']['cache'] ?? null;
        if ($cache === DRUPAL_CACHE_PER_ROLE) {
            $cache_contexts[] = 'user.roles';
        } elseif ($cache === DRUPAL_CACHE_PER_USER) {
            $cache_contexts[] = 'user';
        } elseif ($cache === DRUPAL_CACHE_PER_PAGE) {
            $cache_contexts[] = 'url.path';
        }
        return $cache_contexts;
    }
}
