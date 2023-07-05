<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Render;

use Drupal\Core\Render\AttachmentsInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @phpstan-type Attachments array{drupalSettings?: array<string, mixed>, placeholders?: array<string, mixed>, library?: string[]}
 */
final class AttachmentResponseSubscriber implements EventSubscriberInterface
{
    /**
     * @var array
     * @phpstan-var Attachments
     */
    private array $attachments = [];

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => ['onResponse', 100],
        ];
    }

    public function onResponse(ResponseEvent $event): void
    {
        $response = $event->getResponse();
        if (!$response instanceof AttachmentsInterface) {
            return;
        }
        $response->addAttachments($this->attachments);
    }

    /**
     * @param array $attachments
     * @phpstan-param Attachments $attachments
     */
    public function addAttachments(array $attachments): void
    {
        $this->attachments = BubbleableMetadata::mergeAttachments(
            $this->attachments,
            $attachments
        );
    }
}
