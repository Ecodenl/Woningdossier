<?php

namespace App\Helpers\Cache;

class Step extends BaseCache {

    const CACHE_KEY_GET_ORDERED = 'Step_getOrdered';

    public static function getOrdered(){
        return \Cache::remember(
            self::getCacheKey(static::CACHE_KEY_GET_ORDERED),
            config('hoomdossier.cache.times.default'),
            function () {
                return \App\Models\Step::ordered()->get();
            }
        );
    }
}