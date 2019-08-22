<?php

namespace App\Helpers\Cache;

class Step
{
    const CACHE_KEY_GET_ORDERED = 'Step_getOrdered';

    public static function getOrdered()
    {
        return \Cache::remember(
            sprintf(static::CACHE_KEY_GET_ORDERED),
            config('woningdossier.cache.times.default'),
            function () {
                return \App\Models\Step::ordered()->get();
            }
        );
    }
}
