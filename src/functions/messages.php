<?php

declare(strict_types=1);

use Drupal\Core\Render\Markup;

/**
 * @return string[][]|\Drupal\Component\Render\MarkupInterface[][]
 */
function drupal_set_message(
    null|string|\Stringable $message = null,
    string $type = 'status',
    bool $repeat = true
): array {
    $messenger = \Drupal::messenger();
    if ($message !== null) {
        $messenger->addMessage(Markup::create($message), $type, $repeat);
    }
    return $messenger->all();
}
