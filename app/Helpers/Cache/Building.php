<?php

namespace App\Helpers\Cache;

use App\Models\InputSource;
use Illuminate\Support\Facades\Cache;
use App\Models\Building as BuildingModel;

class Building extends BaseCache
{
    const CACHE_KEY_FIND = 'Building_find_%s';

    public static function find(int $id): ?BuildingModel
    {
        return Cache::remember(
            self::getCacheKey(static::CACHE_KEY_FIND, $id),
            config('hoomdossier.cache.times.default'),
            function () use ($id) {
                return BuildingModel::find($id);
            }
        );
    }

    public static function wipe($id)
    {
        static::clear(self::getCacheKey(static::CACHE_KEY_FIND, $id));
    }
}
