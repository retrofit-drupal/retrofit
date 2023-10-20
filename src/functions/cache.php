<?php

declare(strict_types=1);

use Drupal\Core\Cache\CacheBackendInterface;

const CACHE_PERMANENT = CacheBackendInterface::CACHE_PERMANENT;

function _cache_get_object($bin)
{
    return \Drupal::cache($bin);
}

function cache_get($cid, $bin = 'default')
{
    if ($bin == 'cache') {
        $bin = 'default';
    }
    return _cache_get_object($bin)->get($cid);
}

function cache_get_multiple(array &$cids, $bin = 'default')
{
    if ($bin == 'cache') {
        $bin = 'default';
    }
    return _cache_get_object($bin)->getMultiple($cids);
}

function cache_set($cid, $data, $bin = 'default', $expire = CACHE_PERMANENT)
{
    if ($bin == 'cache') {
        $bin = 'default';
    }
    return _cache_get_object($bin)->set($cid, $data, $expire);
}

function cache_clear_all($cid = null, $bin = null, $wildcard = false)
{
    if ($bin == 'cache') {
        $bin = 'default';
    }
    if (!isset($cid) && !isset($bin)) {
        try {
            _cache_get_object('render')->deleteAll();
            _cache_get_object('page')->deleteAll();
            _cache_get_object('dynamic_page_cache')->deleteAll();
        } catch (\Exception) {
        }
    }
    return _cache_get_object($bin)->deleteAll();
}

function cache_is_empty($bin)
{
    // @todo consequences of this?
    return false;
}
