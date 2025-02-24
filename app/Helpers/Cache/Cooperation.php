<?php

namespace App\Helpers\Cache;

use App\Models\Cooperation as CooperationModel;
use Illuminate\Support\Facades\Cache;

class Cooperation extends BaseCache
{
    const string CACHE_KEY_FIND = 'Cooperation_find_%s';

    public static function find(int $id): ?CooperationModel
    {
        return Cache::remember(
            self::getCacheKey(static::CACHE_KEY_FIND, $id),
            config('hoomdossier.cache.times.default'),
            function () use ($id) {
                return CooperationModel::where('id', '=', $id)->first();
            }
        );
    }

    /**
     * Method to forget the cooperation cache with specific key.
     */
    public static function wipe(string $cacheKey, int $cooperationId): void
    {
        static::clear(self::getCacheKey($cacheKey, $cooperationId));
    }
}
