<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Tests\Utils;

use Drupal\Core\Extension\ModuleHandlerInterface;

final class TestModuleHandler implements ModuleHandlerInterface
{
    public function __construct(
        private readonly ModuleHandlerInterface $inner,
    ) {
    }

    public function load($name)
    {
        return $this->inner->load($name);
    }

    public function loadAll()
    {
        return $this->inner->loadAll();
    }

    public function isLoaded()
    {
        return $this->inner->isLoaded();
    }

    public function reload()
    {
        return $this->inner->reload();
    }

    public function getModuleList()
    {
        return $this->inner->getModuleList();
    }

    public function getModule($name)
    {
        return $this->inner->getModule($name);
    }

    public function setModuleList(array $module_list = [])
    {
        return $this->inner->setModuleList($module_list);
    }

    public function addModule($name, $path)
    {
        return $this->inner->addModule($name, $path);
    }

    public function addProfile($name, $path)
    {
        return $this->inner->addProfile($name, $path);
    }

    public function buildModuleDependencies(array $modules)
    {
        return $this->inner->buildModuleDependencies($modules);
    }

    public function moduleExists($module)
    {
        return $this->inner->moduleExists($module);
    }

    public function loadAllIncludes($type, $name = null)
    {
        return $this->inner->loadAllIncludes($type, $name);
    }

    public function loadInclude($module, $type, $name = null)
    {
        return $this->inner->loadInclude($module, $type, $name);
    }

    public function getHookInfo()
    {
        return $this->inner->getHookInfo();
    }

    public function writeCache()
    {
        return $this->inner->writeCache();
    }

    public function resetImplementations()
    {
        return $this->inner->resetImplementations();
    }

    public function hasImplementations(string $hook, $modules = null): bool
    {
        return $this->inner->hasImplementations($hook, $modules);
    }

    public function invokeAllWith(string $hook, callable $callback): void
    {
        $this->inner->invokeAllWith($hook, $callback);
    }

    public function invoke($module, $hook, array $args = [])
    {
        return $this->inner->invoke($module, $hook, $args);
    }

    public function invokeAll($hook, array $args = [])
    {
        return $this->inner->invokeAll($hook, $args);
    }

    public function invokeDeprecated(
        $description,
        $module,
        $hook,
        array $args = []
    ) {
        return $this->inner->invokeDeprecated($description, $module, $hook, $args);
    }

    public function invokeAllDeprecated($description, $hook, array $args = [])
    {
        return $this->inner->invokeAllDeprecated($description, $hook, $args);
    }

    public function alter($type, &$data, &$context1 = null, &$context2 = null)
    {
        return $this->inner->alter($type, $data, $context1, $context2);
    }

    public function alterDeprecated(
        $description,
        $type,
        &$data,
        &$context1 = null,
        &$context2 = null
    ) {
        return $this->inner->alterDeprecated($description, $type, $data, $context1, $context2);
    }

    public function getModuleDirectories()
    {
        return $this->inner->getModuleDirectories();
    }

    public function getName($module)
    {
        try {
            return $this->inner->getName($module);
        } catch (\Exception) {
            return $module;
        }
    }

    public function destruct(): void
    {
        if (method_exists($this->inner, 'destruct')) {
          $this->inner->destruct();
        }
    }
}
