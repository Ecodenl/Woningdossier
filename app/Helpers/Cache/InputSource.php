<?php

namespace App\Helpers\Cache;

class InputSource extends BaseCache {

    const CACHE_KEY_GET_ORDERED = 'InputSource_getOrdered';
    const CACHE_KEY_FIND = 'InputSource_find_%s';

    public static function getOrdered(){
        return \Cache::remember(
            self::getCacheKey(static::CACHE_KEY_GET_ORDERED),
            config('hoomdossier.cache.times.default'),
            function () {
                return \App\Models\InputSource::orderBy('order', 'desc')->get();
            }
        );
    }

    /**
     * @param int $id
     *
     * @return \App\Models\InputSource|null
     */
    public static function find($id){
        return \Cache::remember(
            self::getCacheKey(static::CACHE_KEY_FIND, $id),
            config('hoomdossier.cache.times.default'),
            function () use ($id) {
                return \App\Models\InputSource::find($id);
            }
        );
    }
}