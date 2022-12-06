<?php

namespace App\Traits;

use App\Helpers\Cache\BaseCache;
use Illuminate\Support\Facades\Cache;

trait HasShortTrait
{
    /**
     * Find a record by its short.
     *
     * @param $short
     *
     * @return mixed
     */
    public static function findByShort($short)
    {
        $cacheKey = 'HasShortTrait_find_by_short_%s_%s';
        $className = get_class(self::getModel());

        return BaseCache::cacheModel(
            BaseCache::getCacheKey($cacheKey, $className, $short),
            self::whereShort($short)
        );
    }

    /**
     * Find multiple records by a set of shorts
     *
     * @param array  $shorts
     *
     * @return mixed
     */
    public static function findByShorts(array $shorts)
    {
        // TODO: Check if we can cache empty arrays, or if we should implement something like above
        $cacheKey = 'HasShortTrait_find_by_shorts_%s_%s';
        $className = get_class(self::getModel());

        // try to cache it so we get some medioker fast stuff
        return Cache::remember(
            BaseCache::getCacheKey($cacheKey, $className, implode(',', $shorts)),
            config('hoomdossier.cache.times.default'),
            function () use ($shorts) {
                return self::whereIn('short', $shorts)->get();
            }
        );
    }
}
