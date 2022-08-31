<?php

namespace App\Helpers\Cache;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class Scan extends BaseCache
{
    const CACHE_KEY_ALL_SHORTS = 'Scan_allShorts';

    public static function allShorts(): array
    {
        return Cache::remember(
            self::getCacheKey(static::CACHE_KEY_ALL_SHORTS),
            config('hoomdossier.cache.times.default'),
            function () {
                if (Schema::hasTable('scans')) {
                    return \App\Models\Scan::pluck('short')->toArray();
                }
                return [];
            }
        );
    }
}
