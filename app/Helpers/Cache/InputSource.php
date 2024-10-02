<?php

namespace App\Helpers\Cache;

use App\Models\InputSource as InputModel;
use Illuminate\Support\Facades\Cache;

class InputSource extends BaseCache
{
    const CACHE_KEY_GET_ORDERED = 'InputSource_getOrdered';
    const CACHE_KEY_FIND = 'InputSource_find_%s';

    public static function getOrdered()
    {
        return Cache::remember(
            self::getCacheKey(static::CACHE_KEY_GET_ORDERED),
            config('hoomdossier.cache.times.default'),
            function () {
                return InputModel::orderBy('order', 'desc')->get();
            }
        );
    }

    public static function find(int $id): ?InputModel
    {
        return Cache::remember(
            self::getCacheKey(static::CACHE_KEY_FIND, $id),
            config('hoomdossier.cache.times.default'),
            function () use ($id) {
                return InputModel::find($id);
            }
        );
    }
}
