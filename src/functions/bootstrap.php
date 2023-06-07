<?php

declare(strict_types=1);

function check_plain($text)
{
    return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}
