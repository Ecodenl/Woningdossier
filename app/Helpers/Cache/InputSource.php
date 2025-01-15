<?php

namespace App\Helpers\Cache;

use App\Models\InputSource as InputSourceModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class InputSource extends BaseCache
{
    const string CACHE_KEY_GET_ORDERED = 'InputSource_getOrdered';
    const string CACHE_KEY_FIND = 'InputSource_find_%s';

    public static function getOrdered(): Collection
    {
        return Cache::remember(
            self::getCacheKey(static::CACHE_KEY_GET_ORDERED),
            config('hoomdossier.cache.times.default'),
            function () {
                return InputSourceModel::orderBy('order', 'desc')->get();
            }
        );
    }

    public static function find(int $id): ?InputSourceModel
    {
        return Cache::remember(
            self::getCacheKey(static::CACHE_KEY_FIND, $id),
            config('hoomdossier.cache.times.default'),
            function () use ($id) {
                return InputSourceModel::find($id);
            }
        );
    }
}
