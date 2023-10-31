<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Tests\Unit\Render;

use Drupal\Core\Render\AttachmentsResponseProcessorInterface;
use Drupal\Core\Render\HtmlResponse;
use PHPUnit\Framework\TestCase;
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
        $sut = new RetrofitHtmlResponseAttachmentsProcessor($inner);
        $sut->processAttachments($response);
        self::assertEquals(
            [
                'library' => [
                    'foo/bar'
                ],
            ],
            $response->getAttachments()
        );
    }
}
