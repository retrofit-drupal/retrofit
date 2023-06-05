<?php

declare(strict_types=1);

namespace Retrofit\Drupal;

use Drupal\Core\Database\Database as CoreDb;

final class DB
{
    public static function get(array &$options = [])
    {
        $target = $options['target'] ?? 'default';
        unset($options['target']);
        return CoreDb::getConnection($target);
    }
}
