<?php

namespace App\Helpers\Cache;

use Illuminate\Support\Facades\Cache;
use App\Models\Interest as InterestModel;

class Interest extends BaseCache
{
    const CACHE_KEY_GET_ORDERED = 'Interest_getOrdered';

    public static function getOrdered()
    {
        return Cache::rememberForever(
            self::getCacheKey(static::CACHE_KEY_GET_ORDERED),
            function () {
                return InterestModel::orderBy('order')->get();
            }
        );
    }
}
