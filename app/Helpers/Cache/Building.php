<?php

namespace App\Helpers\Cache;

use App\Models\InputSource;
use Illuminate\Support\Facades\Cache;

class Building extends BaseCache
{
    const CACHE_KEY_FIND = 'Building_find_%s';

    /**
     * @param int $id
     *
     * @return \App\Models\InputSource|null
     */
    public static function find(int $id): ?InputSource
    {
        return Cache::remember(
            self::getCacheKey(static::CACHE_KEY_FIND, $id),
            config('hoomdossier.cache.times.default'),
            function () use ($id) {
                return \App\Models\Building::find($id);
            }
        );
    }

    public static function wipe($id)
    {
        static::clear(self::getCacheKey(static::CACHE_KEY_FIND, $id));
    }
}
