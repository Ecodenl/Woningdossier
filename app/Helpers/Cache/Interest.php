<?php

namespace App\Helpers\Cache;

class Interest extends BaseCache
{
    const CACHE_KEY_GET_ORDERED = 'Interest_getOrdered';

    public static function getOrdered()
    {
        return \Cache::rememberForever(
            self::getCacheKey(static::CACHE_KEY_GET_ORDERED),
            function () {
                return \App\Models\Interest::orderBy('order')->get();
            }
        );
    }
}