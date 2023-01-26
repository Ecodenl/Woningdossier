<?php

namespace App\Helpers\Cache;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class BaseCache
{
    /**
     * Returns the cache key for a particular format and parameters.
     *
     * @param $string
     * @param mixed ...$parameters
     *
     * @return string
     */
    public static function getCacheKey($string, ...$parameters): string
    {
        $prefix = config('hoomdossier.cache.prefix', '');

        return $prefix.sprintf($string, ...$parameters);
    }

    /**
     * Returns the cache key for a particular format and parameters, prefixed with the current cooperation.
     *
     * @param $string
     * @param mixed ...$parameters
     *
     * @return string
     */
    public static function getCooperationCacheKey($string, ...$parameters): string
    {
        $prefix = config('hoomdossier.cache.prefix', '');

        $cooperation = request()->route('cooperation');

        if ($cooperation instanceof \App\Models\Cooperation) {
            $prefix .= "{$cooperation->slug}_";
        }

        return $prefix.sprintf($string, ...$parameters);
    }

    public static function cacheModel(string $cacheKey, Builder $query): ?Model
    {
        $result = Cache::remember(
            $cacheKey,
            config('hoomdossier.cache.times.default'),
            function () use ($query) {
                $result = $query->first();
                return is_null($result) ? false : $result;
            }
        );

        // If the cache has saved "false", we return null. Cache can't save null.
        return $result instanceof Model ? $result : null;
    }

    public static function clear(string $key)
    {
        Cache::forget($key);
    }
}
