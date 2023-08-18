<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Tests\Utils;

use Drupal\Core\Config\ExtensionInstallStorage;
use Drupal\Core\Extension\ExtensionDiscovery;

final class TestExtensionInstallStorage extends ExtensionInstallStorage
{
    /**
     * @return array<string, string>
     */
    protected function getAllFolders(): array
    {
        // Skips theme scanning due to core not defining a variable.
        // It doesn't define `$theme_list` ahead of time.
        if ($this->folders === null) {
            $this->folders = [];
            $this->folders += $this->getCoreNames();
            $extensions = $this->configStorage->read('core.extension');
            // @todo Remove this scan as part of https://www.drupal.org/node/2186491
            $listing = new ExtensionDiscovery(\Drupal::root());
            if (!empty($extensions['module'])) {
                $modules = $extensions['module'];
                // Remove the install profile as this is handled later.
                unset($modules[$this->installProfile]);
                $profile_list = $listing->scan('profile');
                if ($this->installProfile && isset($profile_list[$this->installProfile])) {
                    // Prime the \Drupal\Core\Extension\ExtensionList::getPathname()
                    // static cache with the profile info file location so we can use
                    // ExtensionList::getPath() on the active profile during the module
                    // scan.
                    // @todo Remove as part of https://www.drupal.org/node/2186491
                    /** @var \Drupal\Core\Extension\ProfileExtensionList $profile_extension_list */
                    $profile_extension_list = \Drupal::service('extension.list.profile');
                    $profile_extension_list->setPathname(
                        $this->installProfile,
                        $profile_list[$this->installProfile]->getPathname()
                    );
                }
                $module_list_scan = $listing->scan('module');
                $module_list = [];
                foreach (array_keys($modules) as $module) {
                    if (isset($module_list_scan[$module])) {
                        $module_list[$module] = $module_list_scan[$module];
                    }
                }
                $this->folders += $this->getComponentNames($module_list);
            }
        }
        return $this->folders;
    }
}
