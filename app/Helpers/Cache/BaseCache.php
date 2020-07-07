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

        $cooperation = request()->route('cooperation');
        
        if ($cooperation instanceof \App\Models\Cooperation) {
            $prefix.="{$cooperation->slug}_";
        }

        return $prefix.sprintf($string, ...$parameters);
    }
}
