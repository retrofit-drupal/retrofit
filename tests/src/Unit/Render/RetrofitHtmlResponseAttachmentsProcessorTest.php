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
     * @covers ::processAttachments
     */
    public function testProcessAttachments(): void
    {
        $response = new HtmlResponse('');
        $response->setAttachments([
            'library' => [
                ['foo', 'bar']
            ],
        ]);
        $inner = $this->createMock(AttachmentsResponseProcessorInterface::class);
        $inner->expects(self::once())
            ->method('processAttachments')
            ->with($response);
        $jsCollectionRenderer = $this->createMock(AssetCollectionRendererInterface::class);
        $libraryDiscovery = $this->createMock(RetrofitLibraryDiscovery::class);
        $sut = new RetrofitHtmlResponseAttachmentsProcessor($inner, $jsCollectionRenderer, $libraryDiscovery);
        $sut->processAttachments($response);
        self::assertEquals(
            [
                'library' => [
                    'foo/bar',
                    'retrofit/gJNwAVQXZnj7Dd9xElORcaSqGosZXm-cTB8t-EgznjU',
                ],
            ],
            $response->getAttachments()
        );
    }
}
