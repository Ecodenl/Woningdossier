<?php

namespace App\Helpers\Cache;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class Step extends BaseCache
{
    const CACHE_KEY_GET_ORDERED = 'Step_getOrdered';
    const CACHE_KEY_ALL_SLUGS = 'Step_allSlugs';

    public static function getOrdered()
    {
        return \Cache::remember(
            self::getCacheKey(static::CACHE_KEY_GET_ORDERED),
            config('hoomdossier.cache.times.default'),
            function () {
                return \App\Models\Step::ordered()->get();
            }
        );
    }

    public static function allSlugs(): array
    {
        return Cache::remember(
            self::getCacheKey(static::CACHE_KEY_ALL_SLUGS),
            config('hoomdossier.cache.times.default'),
            function () {
                if (Schema::hasTable('steps')) {
                    return \App\Models\Step::pluck('slug')->toArray();
                }
                return [];
            }
        );
    }
}
