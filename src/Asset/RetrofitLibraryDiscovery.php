<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Asset;

use Drupal\Core\Asset\LibraryDiscoveryInterface;

class RetrofitLibraryDiscovery implements LibraryDiscoveryInterface
{
    /**
     * @var array<mixed[]>
     */
    protected array $retrofitLibraries = [];

    public function __construct(
        private readonly LibraryDiscoveryInterface $inner,
    ) {
    }

    /**
     * @param string $extension
     * @return array<mixed[]>
     */
    public function getLibrariesByExtension($extension): array
    {
        return match ($extension) {
            'retrofit' => $this->retrofitLibraries,
            default => $this->inner->getLibrariesByExtension($extension),
        };
    }

    /**
     * @param string $extension
     * @param string $name
     * @return mixed[]|false
     */
    public function getLibraryByName($extension, $name): array|false
    {
        return match ($extension) {
            'retrofit' => $this->retrofitLibraries[$name] ?? false,
            default => $this->inner->getLibraryByName($extension, $name),
        };
    }

    public function clearCachedDefinitions(): void
    {
        $this->inner->clearCachedDefinitions();
    }

    /**
     * @param array{
     *   css?: array<string|int, array{group?: int}>,
     *   js?: mixed[]
     * } $attachments
     */
    public function setRetrofitLibrary(string $key, array $attachments): void
    {
        $this->retrofitLibraries[$key]['license'] ??= [];
        if (!empty($attachments['js'])) {
            $this->retrofitLibraries[$key]['dependencies'][] = 'core/jquery';
            $this->retrofitLibraries[$key]['dependencies'][] = 'core/once';
        }
        foreach (['css', 'js'] as $type) {
            foreach ($attachments[$type] ?? [] as $data => $options) {
                if (!is_array($options)) {
                    $options = ['data' => $options];
                }
                if (!is_numeric($data)) {
                    $options['data'] = $data;
                }
                $options += [
                    'type' => 'file',
                    'version' => -1,
                ];
                switch ($type) {
                    case 'css':
                        $options['weight'] ??= 0;
                        $options['weight'] += match ($options['group'] ?? CSS_DEFAULT) {
                            CSS_SYSTEM => CSS_LAYOUT,
                            CSS_THEME, 100 => CSS_AGGREGATE_THEME,
                            default => CSS_AGGREGATE_DEFAULT,
                        };
                        if (!isset($options['group']) || $options['group'] !== CSS_AGGREGATE_THEME) {
                            $options['group'] = CSS_AGGREGATE_DEFAULT;
                        }
                        break;

                    case 'js':
                        $options['group'] = JS_LIBRARY;
                        $options['minified'] ??= false;
                        break;
                }
                $this->retrofitLibraries[$key][$type][] = $options;
            }
        }
    }
}
