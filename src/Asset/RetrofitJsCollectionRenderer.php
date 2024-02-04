<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Asset;

use Drupal\Core\Asset\AssetCollectionRendererInterface;

/**
 * @todo replace with \Drupal\Core\Asset\AssetResolver decoration
 *
 * \Drupal\Core\Asset\AssetResolver::getJsAssets can return the values tracked in $retrofitFooter
 */
class RetrofitJsCollectionRenderer implements AssetCollectionRendererInterface
{
    /**
     * @var array<array{
     *   '#type': string,
     *   '#tag': string,
     *   '#value'?: string,
     *   '#attributes'?: mixed[],
     * }>
     */
    protected array $retrofitFooter = [];

    public function __construct(
        private readonly AssetCollectionRendererInterface $inner,
    ) {
    }

    /**
     * @param mixed[] $assets
     * @return mixed[]
     */
    public function render(array $assets): array
    {
        $is_footer = isset($assets['retrofit']);
        unset($assets['retrofit']);
        $elements = $this->inner->render($assets);
        if ($is_footer && !empty($this->retrofitFooter)) {
            $elements = array_merge($elements, $this->retrofitFooter);
        }
        return $elements;
    }

    /**
     * @param array{
     *   '#type': string,
     *   '#tag': string,
     *   '#value'?: string,
     *   '#attributes'?: mixed[],
     * } $element
     *
     * @todo seems like method would be better on\Drupal\Core\Asset\AssetResolver::getJsAssets
     *  then this renderer does not need to be decorated
     */
    public function addRetrofitFooter(array $element): void
    {
        $this->retrofitFooter[] = $element;
    }
}
