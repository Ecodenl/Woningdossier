<?php

namespace App\Helpers\Cache;

use Illuminate\Support\Facades\Cache;
use App\Models\Building as BuildingModel;

class Building extends BaseCache
{
    const string CACHE_KEY_FIND = 'Building_find_%s';

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

    public static function wipe(int $id): void
    {
        static::clear(self::getCacheKey(static::CACHE_KEY_FIND, $id));
    }
}
