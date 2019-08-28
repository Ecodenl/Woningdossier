<?php

namespace App\Helpers\Cache;

class Status extends BaseCache {

    const CACHE_KEY_FIND = 'Status_find_%s';

    /**
     * @param  int  $id
     *
     * @return \App\Models\Status|null
     */
    public static function find($id)
    {
        return \Cache::remember(
            self::getCacheKey(static::CACHE_KEY_FIND, $id),
            config('hoomdossier.cache.times.default'),
            function () use ($id) {
                return \App\Models\Status::find($id);
            }
        );
    }
}