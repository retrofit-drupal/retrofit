<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Render;

use Drupal\Core\Render\AttachmentsInterface;
use Drupal\Core\Render\AttachmentsResponseProcessorInterface;

final class RetrofitHtmlResponseAttachmentsProcessor implements AttachmentsResponseProcessorInterface
{
    public function __construct(
        private readonly AttachmentsResponseProcessorInterface $inner,
    ) {
    }

    public function processAttachments(AttachmentsInterface $response)
    {
        $attachments = $response->getAttachments();
        // @todo log these removals?
        // @todo what about `settings` now `drupalSettings.
        unset($attachments['css'], $attachments['js']);
        $response->setAttachments($attachments);
        return $this->inner->processAttachments($response);
    }
}
