<?php

namespace App\Helpers\Cache;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class Scan extends BaseCache
{
    const CACHE_KEY_ALL_SHORTS = 'Scan_allShorts';
    const CACHE_KEY_SIMPLE_SHORTS = 'Scan_simpleShorts';
    const CACHE_KEY_EXPERT_SHORTS = 'Scan_expertShorts';

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

    public static function simpleShorts(): array
    {
        return Cache::remember(
            self::getCacheKey(static::CACHE_KEY_SIMPLE_SHORTS),
            config('hoomdossier.cache.times.default'),
            function () {
                if (Schema::hasTable('scans')) {
                    return \App\Models\Scan::simple()->pluck('short')->toArray();
                }
                return [];
            }
        );
    }

    public static function expertShorts(): array
    {
        return Cache::remember(
            self::getCacheKey(static::CACHE_KEY_EXPERT_SHORTS),
            config('hoomdossier.cache.times.default'),
            function () {
                if (Schema::hasTable('scans')) {
                    return \App\Models\Scan::expert()->pluck('short')->toArray();
                }
                return [];
            }
        );
    }
}
