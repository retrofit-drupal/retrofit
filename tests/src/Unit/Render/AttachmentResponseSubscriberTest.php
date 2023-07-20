<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Tests\Unit\Render;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Render\AttachmentsInterface;
use Drupal\Core\Render\HtmlResponse;
use PHPUnit\Framework\TestCase;
use Retrofit\Drupal\Render\AttachmentResponseSubscriber;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class AttachmentResponseSubscriberTest extends TestCase
{
    public function testAddAttachments(): void
    {
        $sut = new AttachmentResponseSubscriber();
        $container = new ContainerBuilder();
        $container->set(AttachmentResponseSubscriber::class, $sut);
        \Drupal::setContainer($container);

        drupal_add_library('system', 'jquery');
        drupal_add_js(['hello' => 'World'], ['type' => 'setting']);
        drupal_add_css('https://example.com/cdn.js');

        $response = new HtmlResponse();
        $event = new ResponseEvent(
            $this->createMock(HttpKernelInterface::class),
            new Request(),
            HttpKernelInterface::MAIN_REQUEST,
            $response
        );
        $sut->onResponse($event);

        self::assertEquals([
            'library' => ['core/jquery'],
            'drupalSettings' => ['hello' => 'World'],
            'css' => [
                'https://example.com/cdn.js' => [],
            ],
        ], $response->getAttachments());
    }
}
