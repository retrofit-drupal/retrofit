<?php

declare(strict_types=1);

use Drupal\Component\Render\MarkupInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Extension\ExtensionPathResolver;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\Render\RendererInterface;
use Retrofit\Drupal\Render\AttachmentResponseSubscriber;

/**
 * @todo flush out
 * this cannot call Url objects because they may generate routes and could
 * cause a recurvise router rebuild. Copy the original code from D7.
 *
 * @link https://git.drupalcode.org/project/drupal/-/blob/7.x/includes/common.inc#L2300
 */
function url(?string $path = null, array $options = []): string
{
    if ($path === null) {
        return '/';
    }

    if ($path[0] !== '/') {
        $path = "/$path";
    }
    return $path;
}

/**
 * @param array{attributes?: array<string, string[]>, html?: bool} $options
 */
function l(string $text, string $path, array $options = []): string
{
    // Merge in defaults.
    $options += [
      'attributes' => [],
      'html' => false,
    ];
    return '<a href="' . check_plain(url($path, $options)) . '"' .
      drupal_attributes($options['attributes']) . '>' .
      ($options['html'] ? $text : check_plain($text)) . '</a>';
}

/**
 * @param array<string, string|string[]> $attributes
 */
function drupal_attributes(array $attributes = []): string
{
    foreach ($attributes as $attribute => &$data) {
        $data = implode(' ', (array) $data);
        $data = $attribute . '="' . check_plain($data) . '"';
    }
    // @note: PHPStan doesn't recognize the shape of $attributes is re-written
    // by reference.
    // @phpstan-ignore-next-line
    return $attributes ? ' ' . implode(' ', $attributes) : '';
}

/**
 * @param array<int, int|string>|false $ids
 * @param array<string, mixed> $conditions
 *
 * @return array<int|string, EntityInterface>
 */
function entity_load(string $entity_type, array|false $ids = false, array $conditions = [], bool $reset = false): array
{
    $controller = entity_get_controller($entity_type);
    if ($reset) {
        $controller->resetCache();
    }
    if ($conditions === []) {
        return $controller->loadMultiple($ids ?: null);
    }
    return $controller->loadByProperties($conditions);
}

function entity_load_unchanged(string $entity_type, int|string $id): ?EntityInterface
{
    return entity_get_controller($entity_type)->loadUnchanged($id);
}

function entity_get_controller(string $entity_type): EntityStorageInterface
{
    // @todo should this return the storage or a decorated storage?
    return \Drupal::entityTypeManager()->getStorage($entity_type);
}

function entity_uri(string $entity_type, EntityInterface $entity): string
{
    // @phpstan-ignore-next-line
    return $entity->toUrl()->toString();
}

/**
 * @return array{int|string|null, int|string|null, string}
 */
function entity_extract_ids(string $entity_type, EntityInterface $entity): array
{
    return [
      $entity->id(),
      $entity instanceof RevisionableInterface ? $entity->getRevisionId() : null,
      $entity->bundle()
    ];
}

function entity_language(string $entity_type, EntityInterface $entity): ?string
{
    $langcode = $entity->language()->getId();
    return $langcode === LanguageInterface::LANGCODE_NOT_SPECIFIED ? null : $langcode;
}

/**
 * @param array<string, mixed> $elements
 * @return array<string, mixed>
 */
function element_children(array &$elements, bool $sort = false): array
{
    return Element::children($elements, $sort);
}

function drupal_get_path(string $type, string $name): string
{
    $pathResolver = \Drupal::service('extension.path.resolver');
    assert($pathResolver instanceof ExtensionPathResolver);
    return $pathResolver->getPath($type, $name);
}

/**
 * @param array<string, mixed> $element
 * @param string[]|null $children_keys
 */
function drupal_render_children(array &$element, array $children_keys = null): string
{
    if ($children_keys === null) {
        $children_keys = element_children($element);
    }
    $output = '';
    foreach ($children_keys as $key) {
        if (!empty($element[$key])) {
            $output .= drupal_render($element[$key]);
        }
    }
    return $output;
}

/**
 * @param array<string, mixed> $elements
 */
function drupal_render(array &$elements): MarkupInterface
{
    $renderer = \Drupal::service('renderer');
    assert($renderer instanceof RendererInterface);
    return $renderer->render($elements);
}

/**
 * @param array<string, mixed>|mixed $element
 * @return string|mixed
 */
function render(&$element)
{
    if (is_array($element)) {
        show($element);
        return drupal_render($element);
    }

    // Safe-guard for inappropriate use of render() on flat variables: return
    // the variable as-is.
    return $element;
}

function drupal_add_library(string $module, string $name, ?bool $every_page = null): void
{
    $attachment_subscriber = \Drupal::getContainer()->get(AttachmentResponseSubscriber::class);
    assert($attachment_subscriber instanceof AttachmentResponseSubscriber);

    $module = match ($name) {
        'drupal.ajax', 'jquery' => 'core',
        default => $module
    };

    $library = "$module/$name";

    $attachment_subscriber->addAttachments([
        'library' => [$library],
    ]);
}
