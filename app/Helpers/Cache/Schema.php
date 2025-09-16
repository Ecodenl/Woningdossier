<?php

namespace App\Helpers\Cache;

use Illuminate\Support\Facades\Cache;

class Schema extends BaseCache
{
    const string CACHE_KEY_HAS_COLUMN = 'Schema_hasColumn_%s_%s';

    public static function hasColumn($table, $attribute): bool
    {
        return Cache::rememberForever(
            self::getCacheKey(static::CACHE_KEY_HAS_COLUMN, $table, $attribute),
            function () use ($table, $attribute) {
                return \Illuminate\Support\Facades\Schema::hasColumn($table, $attribute);
            }
        );
    }
}
