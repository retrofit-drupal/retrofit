<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Render;

use Drupal\Component\Utility\Crypt;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Asset\LibraryDiscoveryInterface;
use Drupal\Core\Render\AttachmentsInterface;
use Drupal\Core\Render\AttachmentsResponseProcessorInterface;
use Retrofit\Drupal\Asset\RetrofitLibraryDiscovery;

final class RetrofitHtmlResponseAttachmentsProcessor implements AttachmentsResponseProcessorInterface
{
    public function __construct(
        private readonly AttachmentsResponseProcessorInterface $inner,
        private readonly LibraryDiscoveryInterface $libraryDiscovery,
    ) {
    }

    public function processAttachments(AttachmentsInterface $response)
    {
        $attachments = $response->getAttachments();
        if (isset($attachments['library']) && is_array($attachments['library'])) {
            foreach ($attachments['library'] as $key => $item) {
                if (is_array($item)) {
                    $item[0] = match ($item[0]) {
                        'drupal.ajax', 'jquery' => 'core',
                        default => $item[0],
                    };
                    $attachments['library'][$key] = implode('/', $item);
                }
            }
        }
        if (isset($attachments['css']) && is_array($attachments['css'])) {
            foreach ($attachments['css'] as $key => $item) {
                if (is_array($item) && isset($item['type'], $item['data']) && $item['type'] === 'inline') {
                    $element = [
                        '#tag' => 'style',
                        '#value' => $item['data'],
                        '#weight' => $item['weight'] ?? 0,
                    ];
                    unset(
                        $item['data'],
                        $item['type'],
                        $item['basename'],
                        $item['group'],
                        $item['every_page'],
                        $item['weight'],
                        $item['preprocess'],
                        $item['browsers'],
                    );
                    $element['#attributes'] = $item;
                    $attachments['html_head'][] = [
                        $element,
                        "retrofit:$key",
                    ];
                    unset($attachments['css'][$key]);
                }
            }
        }
        if (isset($attachments['js']) && is_array($attachments['js'])) {
            foreach ($attachments['js'] as $key => $item) {
                if (is_array($item) && isset($item['type'], $item['data'])) {
                    switch ($item['type']) {
                        case 'inline':
                            $element = [
                                '#tag' => 'script',
                                '#value' => $item['data'],
                                '#weight' => $item['weight'] ?? 0,
                            ];
                            unset(
                                $item['data'],
                                $item['type'],
                                $item['scope'],
                                $item['group'],
                                $item['every_page'],
                                $item['weight'],
                                $item['requires_jquery'],
                                $item['cache'],
                                $item['preprocess'],
                            );
                            $element['#attributes'] = $item;
                            $attachments['html_head'][] = [
                                $element,
                                "retrofit:$key",
                            ];
                            unset($attachments['js'][$key]);
                            break;

                        case 'setting':
                            $attachments['drupalSettings'] = NestedArray::mergeDeepArray(
                                [$attachments['drupalSettings'] ?? [], $item['data']],
                                true,
                            );
                            unset($attachments['js'][$key]);
                            break;
                    }
                }
            }
        }
        $retrofit_library = [
            'css' => $attachments['css'] ?? [],
            'js' => $attachments['js'] ?? [],
        ];
        unset($attachments['css'], $attachments['js']);
        asort($retrofit_library['css']);
        asort($retrofit_library['js']);
        $name = Crypt::hashBase64(serialize($retrofit_library));
        assert($this->libraryDiscovery instanceof RetrofitLibraryDiscovery);
        $this->libraryDiscovery->setRetrofitLibrary($name, $retrofit_library);
        $attachments['library'][] = "retrofit/$name";
        $response->setAttachments($attachments);
        return $this->inner->processAttachments($response);
    }
}
