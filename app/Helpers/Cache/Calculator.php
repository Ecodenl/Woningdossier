<?php

namespace App\Helpers\Cache;

class Calculator extends BaseCache
{

    const CACHE_PRICE_INDEX = 'Calculator_getPriceIndex_%s';

    public static function getPriceIndex($short)
    {
        return \Cache::remember(
            self::getCacheKey(static::CACHE_PRICE_INDEX, $short),
            config('hoomdossier.cache.times.default'),
            function () use ($short) {
                return \App\Models\PriceIndexing::where('short', $short)->first();
            }
        );

    }

}