<?php

namespace App\Helpers\Cache;

class Building extends BaseCache
{

    const CACHE_KEY_FIND = 'Building_find_%s';

    /**
     * @param int $id
     *
     * @return \App\Models\InputSource|null
     */
    public static function find($id)
    {
        return \Cache::remember(
            self::getCacheKey(static::CACHE_KEY_FIND, $id),
            config('hoomdossier.cache.times.default'),
            function () use ($id) {
                return \App\Models\Building::find($id);
            }
        );
    }

    public static function wipe($id)
    {
        \Cache::forget(self::getCacheKey(static::CACHE_KEY_FIND, $id));
    }
}