<?php

namespace App\Helpers\Cache;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use App\Models\Account as AccountModel;

class Account extends BaseCache
{
    const string CACHE_KEY_FIND = 'Account_find_%s';
    const string CACHE_KEY_USER = 'Account_user_%s';

    public static function find(int $id): ?AccountModel
    {
        return Cache::remember(
            self::getCacheKey(static::CACHE_KEY_FIND, $id),
            config('hoomdossier.cache.times.default'),
            function () use ($id) {
                return AccountModel::find($id);
            }
        );
    }

    public static function user(AccountModel $account): ?User
    {
        return Cache::remember(
            self::getCooperationCacheKey(static::CACHE_KEY_USER, $account->id),
            config('hoomdossier.cache.times.default'),
            function () use ($account) {
                return $account->users()->first();
            }
        );
    }

    public static function wipe(int $id): void
    {
        static::clear(self::getCacheKey(static::CACHE_KEY_FIND, $id));
        static::clear(self::getCooperationCacheKey(static::CACHE_KEY_USER, $id));
    }
}
