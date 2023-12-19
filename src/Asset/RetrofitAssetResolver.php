<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Asset;

use Drupal\Core\Asset\AttachedAssetsInterface;
use Drupal\Core\Asset\AssetResolverInterface;
use Drupal\Core\Language\LanguageInterface;

class RetrofitAssetResolver implements AssetResolverInterface
{
    public function __construct(
        private readonly AssetResolverInterface $inner,
    ) {
    }

    /**
     * @param bool $optimize
     * @return mixed[]
     */
    public function getCssAssets(AttachedAssetsInterface $assets, $optimize, ?LanguageInterface $language = null): array
    {
        return $this->inner->getCssAssets($assets, $optimize, $language);
    }

    /**
     * @param bool $optimize
     * @return mixed[]
     */
    public function getJsAssets(AttachedAssetsInterface $assets, $optimize, ?LanguageInterface $language = null): array
    {
        $js_assets = $this->inner->getJsAssets($assets, $optimize, $language);
        $js_assets[1]['footer'] = true;
        return $js_assets;
    }
}
