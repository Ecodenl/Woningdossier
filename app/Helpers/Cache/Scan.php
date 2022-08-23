<?php

namespace App\Helpers\Cache;

use Illuminate\Support\Facades\Cache;

class Scan extends BaseCache
{
    const CACHE_KEY_ALL_SHORTS = 'Scan_allShorts';

    public static function allShorts()
    {
        return Cache::remember(
            self::getCacheKey(static::CACHE_KEY_ALL_SHORTS),
            config('hoomdossier.cache.times.default'),
            function () {
                return \App\Models\Scan::pluck('short')->toArray();
            }
        );
    }
}
