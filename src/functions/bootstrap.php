<?php

declare(strict_types=1);

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Component\Render\MarkupInterface;
use Drupal\Component\Utility\Crypt;
use Drupal\Core\Controller\TitleResolverInterface;
use Drupal\Core\Extension\ExtensionPathResolver;
use Retrofit\Drupal\Controller\RetrofitTitleResolver;

function check_plain(MarkupInterface|\Stringable|string $text): string
{
    return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

function conf_path(bool $require_settings = true, bool $reset = false): string
{
    $conf = \Drupal::getContainer()->getParameter('site.path');
    assert(is_string($conf));
    return $conf;
}

function drupal_get_filename(string $type, string $name, ?string $filename = null, bool $trigger_error = false): ?string
{
    $pathResolver = \Drupal::service('extension.path.resolver');
    assert($pathResolver instanceof ExtensionPathResolver);
    return $pathResolver->getPathname($type, $name);
}

/**
 * @return false|array{fields: array<string, array{serialize: bool, type: string, default: mixed, 'not null'?: bool}>}
 */
function drupal_get_schema(?string $table = null, ?bool $rebuild = false): array|bool
{
    $schema = &drupal_static(__FUNCTION__);
    if (!isset($schema) || $rebuild) {
        if (!$rebuild && ($cached = \Drupal::cache()->get(__FUNCTION__))) {
            $schema = $cached->data;
        } else {
            $schema = [];
            $module_handler = \Drupal::moduleHandler();
            foreach ($module_handler->getModuleList() as $name => $module) {
                if ($module_handler->loadInclude($name, 'install')) {
                    foreach ((array) $module_handler->invoke($name, 'schema') as $table_name => $table_schema) {
                        if (is_array($table_schema)) {
                            if (empty($table_schema['module'])) {
                                $table_schema['module'] = $name;
                            }
                            if (empty($table_schema['name'])) {
                                $table_schema['name'] = $table_name;
                            }
                            $schema[$table_name] = $table_schema;
                        }
                    }
                }
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

function drupal_hash_base64(string $data): string
{
    return Crypt::hashBase64($data);
}

function drupal_is_https(): bool
{
    return \Drupal::request()->isSecure();
}

function get_t(): string
{
    return 't';
}

function drupal_set_title(?string $title = null, int $output = CHECK_PLAIN): array|string|\Stringable|null
{
    $titleResolver = \Drupal::service('title_resolver');
    assert($titleResolver instanceof TitleResolverInterface);
    if ($title !== null && $titleResolver instanceof RetrofitTitleResolver) {
        $storedTitle = ($output === PASS_THROUGH) ? $title : check_plain($title);
        $titleResolver->setStoredTitle($storedTitle);
    }

    $route = \Drupal::routeMatch()->getRouteObject();
    if ($route === null) {
        return null;
    }

    return $titleResolver->getTitle(\Drupal::request(), $route);
}

function drupal_get_title(): array|string|\Stringable|null
{
    $titleResolver = \Drupal::service('title_resolver');
    assert($titleResolver instanceof TitleResolverInterface);

    $route = \Drupal::routeMatch()->getRouteObject();
    if ($route === null) {
        return null;
    }

    return $titleResolver->getTitle(\Drupal::request(), $route);
}

/**
 * @param array<string, string> $args
 */
function format_string(string $string, array $args = array()): MarkupInterface
{
    return new FormattableMarkup($string, $args);
}

function request_uri(): string
{
    return \Drupal::request()->getRequestUri();
}

/**
 * @param mixed[] $variables
 */
function watchdog(
    string $type,
    string|\Stringable $message,
    array $variables = [],
    int $severity = WATCHDOG_NOTICE,
    ?string $link = null
): void {
    $variables['link'] = $link ?? '';
    \Drupal::logger($type)->log($severity, $message, $variables);
}
