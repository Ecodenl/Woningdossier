<?php

namespace App\Traits;

use App\Helpers\Cache\BaseCache;


trait HasShortTrait {

    /**
     * Find a record by its short
     *
     * @param $short
     * @return mixed
     */
    public static function findByShort($short)
    {

        $cacheKey = 'HasShortTrait_find_by_short_%s_%s';
        $className = get_class(self::getModel());

        // try to cache it so we get some medioker fast stuff
        return \Cache::remember(
            BaseCache::getCacheKey($cacheKey, $className, $short),
            config('hoomdossier.cache.times.default'),
            function () use ($short) {
                return self::whereShort($short)->first();
            }
        );
    }
}
