<?php

declare(strict_types=1);

use Composer\InstalledVersions;
use Drupal\Component\Render\MarkupInterface;
use Drupal\Core\Extension\ExtensionPathResolver;
use Drupal\TestTools\Extension\SchemaInspector;

function check_plain(MarkupInterface|\Stringable|string $text): string
{
    return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

function drupal_get_filename(string $type, string $name, ?string $filename = null, bool $trigger_error = false): ?string
{
    $pathResolver = \Drupal::service('extension.path.resolver');
    assert($pathResolver instanceof ExtensionPathResolver);
    return $pathResolver->getPathname($type, $name);
}

function drupal_get_schema(?string $table = null, ?bool $rebuild = false): array|bool
{
    $loader = require realpath(InstalledVersions::getRootPackage()['install_path']) . '/vendor/autoload.php';
    $loader->add('Drupal\\TestTools', InstalledVersions::getInstallPath('drupal/drupal') . '/core/tests');
    $schema = &drupal_static(__FUNCTION__);
    if (!isset($schema) || $rebuild) {
        if (!$rebuild && ($cached = \Drupal::cache()->get(__FUNCTION__))) {
            $schema = $cached->data;
        } else {
            $schema = [];
            $module_handler = \Drupal::moduleHandler();
            foreach ($module_handler->getModuleList() as $name => $module) {
                $schema += SchemaInspector::getTablesSpecification($module_handler, $name);
            }
            \Drupal::cache()->set(__FUNCTION__, $schema);
        }
    }
    if (!isset($table)) {
        return $schema;
    }
    if (isset($schema[$table])) {
        return $schema[$table];
    } else {
        return false;
    }
}

function get_t(): string
{
    return 't';
}
