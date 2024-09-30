<?php

namespace App\Helpers\Cache;

use App\Models\Status;
use Illuminate\Support\Facades\Cache;

class Status extends BaseCache
{
    const CACHE_KEY_FIND = 'Status_find_%s';

    public static function find(int $id): ?Status
    {
        return Cache::remember(
            self::getCacheKey(static::CACHE_KEY_FIND, $id),
            config('hoomdossier.cache.times.default'),
            function () use ($id) {
                return \App\Models\Status::find($id);
            }
        );
    }
}
