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
    public static function getCacheKey($string, ...$parameters)
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
                \Log::debug('CACHE??');
                return is_null($result) ? false : $result;
            }
        );

        // If the cache has saved "false", we return null. Cache can't save null.
        return $result instanceof Model ? $result : null;
    }
}
