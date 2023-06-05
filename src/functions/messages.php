<?php

declare(strict_types=1);

function drupal_set_message(?\Stringable $message = null, string $type = 'status', bool $repeat = true)
{
    \Drupal::messenger()->addMessage($message, $type, $repeat);
}
