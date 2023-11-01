<?php

declare(strict_types=1);

use Drupal\Core\Database\SupportsTemporaryTablesInterface;
use Retrofit\Drupal\DB;

/**
 * @file
 * Compatibility layer for database.inc
 *
 * @link https://www.drupal.org/node/2993033
 *
 * @todo add back `target` support in `$options`
 */

function db_query(string $query, array $args = [], array $options = [])
{
    return DB::get($options)->query($query, $args, $options);
}

function db_query_range(string $query, string|int $from, string|int $count, array $args = [], array $options = [])
{
    return DB::get($options)->queryRange($query, (int) $from, (int) $count, $args, $options);
}

function db_query_temporary($query, array $args = [], array $options = [])
{
    $connection = DB::get($options);
    if ($connection instanceof SupportsTemporaryTablesInterface) {
        return $connection->queryTemporary($query, $args, $options);
    }
    throw new \RuntimeException('Driver does not support temporary queries');
}

function db_insert(string $table, array $options = [])
{
    return DB::get($options)->insert($table, $options);
}

function db_merge(string $table, array $options = [])
{
    return DB::get($options)->merge($table, $options);
}

function db_update(string $table, array $options = [])
{
    return DB::get($options)->update($table, $options);
}

function db_delete(string $table, array $options = [])
{
    return DB::get($options)->delete($table, $options);
}

function db_truncate(string $table, array $options = [])
{
    return DB::get($options)->truncate($table, $options);
}

function db_select(string $table, ?string $alias = null, array $options = [])
{
    return DB::get($options)->select($table, $alias, $options);
}

function db_transaction(?string $name = null, array $options = [])
{
    return DB::get($options)->startTransaction($name);
}

function db_like(string $arg)
{
    return DB::get()->escapeLike($arg);
}

function db_and()
{
    return db_condition('AND');
}

function db_or()
{
    return db_condition('OR');
}

function db_condition(string $conjunction)
{
    return DB::get()->condition($conjunction);
}

function db_table_exists(string $table): bool
{
    return DB::get()->schema()->tableExists($table);
}
