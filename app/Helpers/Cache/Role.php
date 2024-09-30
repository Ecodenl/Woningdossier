<?php

namespace App\Helpers\Cache;

use App\Models\Role;
use Illuminate\Support\Facades\Cache;

class Role extends BaseCache
{
    const CACHE_KEY_FIND = 'Role_find_%s';
    const CACHE_KEY_FIND_BY_NAME = 'Role_find_by_name_%s';

    public static function find(int $id): ?Role
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
