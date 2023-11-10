<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Routing\Enhancer;

use Drupal\Core\Routing\Enhancer\ParamConversionEnhancer as CoreParamConversionEnhancer;
use Symfony\Component\HttpFoundation\Request;

final class ParamConversionEnhancer extends CoreParamConversionEnhancer
{
    /**
     * @param mixed[] $defaults
     * @return mixed[]
     */
    public function enhance(array $defaults, Request $request): array
    {
        $defaults['_request'] = $request;
        return parent::enhance($defaults, $request);
    }
}
