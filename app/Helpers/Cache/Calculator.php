<?php

namespace App\Helpers\Cache;

use App\Models\PriceIndexing;

class Calculator
{

    const CACHE_PRICE_INDEX = 'Calculator_getPriceIndex_%s';

    public static function getPriceIndex($short)
    {
        return \Cache::remember(
            sprintf(static::CACHE_PRICE_INDEX, $short),
            config('woningdossier.cache.times.default'),
            function () use ($short) {
                return PriceIndexing::where('short', $short)->first();
            }
        );

    }

}