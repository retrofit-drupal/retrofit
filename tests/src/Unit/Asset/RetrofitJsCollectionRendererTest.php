<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Tests\Unit\Asset;

use Drupal\Core\Asset\AssetCollectionRendererInterface;
use PHPUnit\Framework\TestCase;
use Retrofit\Drupal\Asset\RetrofitJsCollectionRenderer;

/**
 * @coversDefaultClass \Retrofit\Drupal\Asset\RetrofitJsCollectionRenderer
 */
final class RetrofitJsCollectionRendererTest extends TestCase
{

    /**
     * @covers ::__construct
     * @covers ::render
     * @covers ::addRetrofitFooter
     */
    public function testRender(): void
    {
        $inner = $this->createMock(AssetCollectionRendererInterface::class);
        $inner->expects($this->exactly(2))
            ->method('render')
            ->with($this->anything())
            ->willReturn([
                [
                    '#type' => 'html_tag',
                    '#tag' => 'meta',
                    '#value' => 'foobar',
                ],
            ]);
        $sut = new RetrofitJsCollectionRenderer($inner);
        $sut->addRetrofitFooter([
            '#type' => 'html_tag',
            '#tag' => 'script',
            '#value' => 'window.foo = "bar"',
        ]);
        self::assertEquals([
            [
                '#type' => 'html_tag',
                '#tag' => 'meta',
                '#value' => 'foobar',
            ],
        ], $sut->render([]));
        self::assertEquals([
            [
                '#type' => 'html_tag',
                '#tag' => 'meta',
                '#value' => 'foobar',
            ],
            [
                '#type' => 'html_tag',
                '#tag' => 'script',
                '#value' => 'window.foo = "bar"',
            ]
        ], $sut->render(['retrofit' => '']));

    }

}
