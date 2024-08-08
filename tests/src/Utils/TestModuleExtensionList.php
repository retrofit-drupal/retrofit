<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Tests\Utils;

use Drupal\Core\Extension\Extension;
use Drupal\Core\Extension\ModuleExtensionList;

final class TestModuleExtensionList extends ModuleExtensionList
{
    protected function doScanExtensions()
    {
        $extensions = parent::doScanExtensions();
        $test_modules = ['menu_example', 'theming_example'];
        foreach ($test_modules as $test_module) {
            $extensions[$test_module] = new Extension(
                $this->root,
                'module',
                "../../tests/data/$test_module/$test_module.info.yml",
                "$test_module.module"
            );
        }
        return $extensions;
    }
}
