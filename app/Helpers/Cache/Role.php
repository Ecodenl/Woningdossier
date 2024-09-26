<?php

namespace App\Helpers\Cache;

use Illuminate\Support\Facades\Cache;

class Role extends BaseCache
{
    const CACHE_KEY_FIND = 'Role_find_%s';
    const CACHE_KEY_FIND_BY_NAME = 'Role_find_by_name_%s';

    /**
     * @param int $id
     *
     * @return \App\Models\Role|null
     */
    public static function find($id)
    {
        return Cache::remember(
            self::getCacheKey(static::CACHE_KEY_FIND, $id),
            config('hoomdossier.cache.times.default'),
            function () use ($id) {
                return \App\Models\Role::find($id);
            }
        );
    }

    public static function findByName($name)
    {
        return Cache::remember(
            self::getCacheKey(static::CACHE_KEY_FIND_BY_NAME, $name),
            config('hoomdossier.cache.times.default'),
            function () use ($name) {
                return \App\Models\Role::byName($name)->first();
            }
        );
    }
}
