<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Tests\Unit\Render;

use Drupal\Core\Asset\AssetCollectionRendererInterface;
use Drupal\Core\Render\AttachmentsResponseProcessorInterface;
use Drupal\Core\Render\HtmlResponse;
use PHPUnit\Framework\TestCase;
use Retrofit\Drupal\Asset\RetrofitLibraryDiscovery;
use Retrofit\Drupal\Render\RetrofitHtmlResponseAttachmentsProcessor;

/**
 * @coversDefaultClass \Retrofit\Drupal\Render\RetrofitHtmlResponseAttachmentsProcessor
 */
final class RetrofitHtmlResponseAttachmentsProcessorTest extends TestCase
{
    /**
     *
     * @param array<string, mixed> $attachments
     * @param array<string, mixed> $expected
     *
     * @covers ::__construct
     * @covers ::processAttachments
     *
     * @dataProvider dataAttachments
     */
    public function testProcessAttachments(array $attachments, array $expected): void
    {
        $response = new HtmlResponse('');
        $response->setAttachments($attachments);
        $inner = $this->createMock(AttachmentsResponseProcessorInterface::class);
        $inner->expects(self::once())
            ->method('processAttachments')
            ->with($response);
        $jsCollectionRenderer = $this->createMock(AssetCollectionRendererInterface::class);
        $libraryDiscovery = $this->createMock(RetrofitLibraryDiscovery::class);
        $sut = new RetrofitHtmlResponseAttachmentsProcessor($inner, $jsCollectionRenderer, $libraryDiscovery);
        $sut->processAttachments($response);
        self::assertEquals($expected, $response->getAttachments());
    }

    /**
     * @return array<string, array<int, array<string, array<int, array<int|string, array<string, array<string,
     *     string>|int|string>|bool|int|string>|string>>>>
     */
    public static function dataAttachments(): array
    {
        return [
            'legacy libraries' => [
                [
                    'library' => [
                        ['foo', 'bar']
                    ],
                ],
                [
                    'library' => [
                        'foo/bar',
                        'retrofit/NXhscRe0440PFpI5dSznEVgmauL25KojD7u4e9aZwOM',
                    ],
                ],
            ],
            'inline css' => [
                [
                    'css' => [
                        [
                            'type' => 'inline',
                            'group' => 0,
                            'weight' => 0,
                            'every_page' => false,
                            'media' => 'all',
                            'preprocess' => true,
                            'data' => '.foo { color: pink }',
                            'browsers' => [],
                        ],
                    ],
                ],
                [
                    'html_head' => [
                        [
                            [
                                '#tag' => 'style',
                                '#value' => '.foo { color: pink }',
                                '#weight' => 0,
                                '#attributes' => [
                                    'media' => 'all',
                                ],
                            ],
                            'retrofit:0',
                        ],
                    ],
                    'library' => [
                        'retrofit/p0pYMgU_NanStScEFSFfzy8t6FiwrwJCbJdc0EbwWk0',
                    ],
                ]
            ],
        ];
    }
}
