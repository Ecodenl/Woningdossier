<?php

namespace App\Helpers\Cache;

use App\Models\PriceIndexing;
use Illuminate\Support\Facades\Cache;

class Calculator extends BaseCache
{
    const string CACHE_PRICE_INDEX = 'Calculator_getPriceIndex_%s';

    public static function getPriceIndex(string $short): ?PriceIndexing
    {
        return Cache::remember(
            self::getCacheKey(static::CACHE_PRICE_INDEX, $short),
            config('hoomdossier.cache.times.default'),
            function () use ($short) {
                return PriceIndexing::where('short', $short)->first();
            }
        );
    }
}
