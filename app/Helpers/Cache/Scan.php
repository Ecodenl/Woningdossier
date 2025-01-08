<?php

namespace App\Helpers\Cache;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use App\Models\Scan as ScanModel;

class Scan extends BaseCache
{
    const string CACHE_KEY_ALL_SHORTS = 'Scan_allShorts';
    const string CACHE_KEY_SIMPLE_SHORTS = 'Scan_simpleShorts';
    const string CACHE_KEY_EXPERT_SHORTS = 'Scan_expertShorts';

    public static function allShorts(): array
    {
        return Cache::remember(
            self::getCacheKey(static::CACHE_KEY_ALL_SHORTS),
            config('hoomdossier.cache.times.default'),
            function () {
                if (Schema::hasTable('scans')) {
                    return ScanModel::pluck('short')->toArray();
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
                    return ScanModel::simpleScans()->pluck('short')->toArray();
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
                    return ScanModel::expertScans()->pluck('short')->toArray();
                }
                return [];
            }
        );
    }
}
