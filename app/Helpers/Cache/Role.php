<?php

namespace App\Helpers\Cache;

use App\Models\Role as RoleModel;
use Illuminate\Support\Facades\Cache;

class Role extends BaseCache
{
    const string CACHE_KEY_FIND = 'Role_find_%s';
    const string CACHE_KEY_FIND_BY_NAME = 'Role_find_by_name_%s';

    public static function find(int $id): ?RoleModel
    {
        return Cache::remember(
            self::getCacheKey(static::CACHE_KEY_FIND, $id),
            config('hoomdossier.cache.times.default'),
            function () use ($id) {
                return RoleModel::find($id);
            }
        );
    }

    public static function findByName(string $name): ?RoleModel
    {
        return Cache::remember(
            self::getCacheKey(static::CACHE_KEY_FIND_BY_NAME, $name),
            config('hoomdossier.cache.times.default'),
            function () use ($name) {
                return RoleModel::byName($name)->first();
            }
        );
    }
}
