<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Tests\Unit\Render;

use Drupal\Core\Asset\AssetCollectionRendererInterface;
use Drupal\Core\Render\AttachmentsResponseProcessorInterface;
use Drupal\Core\Render\HtmlResponse;
use PHPUnit\Framework\TestCase;
use Retrofit\Drupal\Asset\RetrofitJsCollectionRenderer;
use Retrofit\Drupal\Asset\RetrofitLibraryDiscovery;
use Retrofit\Drupal\Render\RetrofitHtmlResponseAttachmentsProcessor;

/**
 * @coversDefaultClass \Retrofit\Drupal\Render\RetrofitHtmlResponseAttachmentsProcessor
 */
final class RetrofitHtmlResponseAttachmentsProcessorTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::processAttachments
     * @covers \Retrofit\Drupal\Asset\RetrofitJsCollectionRenderer::__construct
     * @covers \Retrofit\Drupal\Asset\RetrofitJsCollectionRenderer::addRetrofitFooter
     */
    public function testProcessAttachments(): void
    {
        $response = new HtmlResponse('');
        $response->setAttachments([
            'library' => [
                ['foo', 'bar']
            ],
            'css' => [
                [
                    'data' => '.foo { display: none }',
                    'type' => 'inline',
                ]
            ],
            'js' => [
                [
                    'data' => '(function($){$(function() { $("#accordion").accordion(); })})(jQuery);',
                    'type' => 'inline',
                ],
                [
                    'data' => 'window.foo = "bar"',
                    'type' => 'inline',
                    'scope' => 'header',
                ],
                'somefile.js' => [
                    'type'=> 'file',
                    'scope' => 'header',
                    'data' => 'somefile.js',

                ],
                [
                    'data' => ['hello' => 'World'],
                    'type' => 'setting'
                ]
            ],
        ]);
        $inner = $this->createMock(AttachmentsResponseProcessorInterface::class);
        $inner->expects(self::once())
            ->method('processAttachments')
            ->with($response);
        $jsCollectionRenderer = new RetrofitJsCollectionRenderer($this->createMock(AssetCollectionRendererInterface::class));
        $libraryDiscovery = $this->createMock(RetrofitLibraryDiscovery::class);
        $sut = new RetrofitHtmlResponseAttachmentsProcessor($inner, $jsCollectionRenderer, $libraryDiscovery);
        $sut->processAttachments($response);
        self::assertEquals(
            [
                'library' => [
                    'foo/bar',
                    'retrofit/KIKS7GlZS0NfEYaQslyuchO3L0NFRSrcHqPaMI_gUIU',
                ],
                'html_head' => [
                    [
                        [
                            '#tag' => 'style',
                            '#value' => '.foo { display: none }',
                            '#weight' => 0,
                            '#attributes' => [],
                        ],
                        'retrofit:0',
                    ],
                    [
                        [
                            '#tag' => 'script',
                            '#value' => 'window.foo = "bar"',
                            '#weight' => 0,
                            '#attributes' => [],
                        ],
                        'retrofit:1',
                    ],
                ],
                'drupalSettings' => [
                    'hello' => 'World'
                ],
            ],
            $response->getAttachments()
        );
    }
}
