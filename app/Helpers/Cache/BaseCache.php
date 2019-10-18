<?php

namespace App\Helpers\Cache;

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

        return $prefix.sprintf($string, ...$parameters);
    }
}
