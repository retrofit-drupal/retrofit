<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Asset;

use Drupal\Core\Asset\AssetCollectionRendererInterface;

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
     */
    public function addRetrofitFooter(array $element): void
    {
        $this->retrofitFooter[] = $element;
    }
}
