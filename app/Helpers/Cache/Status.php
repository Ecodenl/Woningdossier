<?php

namespace App\Helpers\Cache;

use App\Models\Status as StatusModel;
use Illuminate\Support\Facades\Cache;

class Status extends BaseCache
{
    const string CACHE_KEY_FIND = 'Status_find_%s';

    public static function find(int $id): ?StatusModel
    {
        return Cache::remember(
            self::getCacheKey(static::CACHE_KEY_FIND, $id),
            config('hoomdossier.cache.times.default'),
            function () use ($id) {
                return StatusModel::find($id);
            }
        );
    }
}
