<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Tests\Utils;

use Drupal\Core\Extension\Extension;
use Drupal\Core\Extension\ThemeExtensionList;

class TestThemeExtensionList extends ThemeExtensionList
{

    protected function doScanExtensions(): array
    {
        $extensions = parent::doScanExtensions();
        $extensions['bartik'] = new Extension(
            $this->root,
            'theme',
            "../../tests/data/bartik/bartik.info.yml",
            'bartik.theme'
        );
        return $extensions;
    }

}
