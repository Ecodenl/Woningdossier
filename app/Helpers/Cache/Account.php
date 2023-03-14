<?php

namespace App\Helpers\Cache;

use Illuminate\Support\Facades\Cache;

class Account extends BaseCache
{
    const CACHE_KEY_FIND = 'Account_find_%s';
    const CACHE_KEY_USER = 'Account_user_%s';

    /**
     * @param int $id
     *
     * @return \App\Models\InputSource|null
     */
    public static function find($id)
    {
        return Cache::remember(
            self::getCacheKey(static::CACHE_KEY_FIND, $id),
            config('hoomdossier.cache.times.default'),
            function () use ($id) {
                return \App\Models\Account::find($id);
            }
        );
    }

    public static function user($account)
    {
        if (! $account instanceof \App\Models\Account) {
            $account = self::find($account);
        }

        return Cache::remember(
            self::getCooperationCacheKey(static::CACHE_KEY_USER, $account->id),
            config('hoomdossier.cache.times.default'),
            function () use ($account) {
                return $account->users()->first();
            }
        );
    }

    public static function wipe($id)
    {
        static::clear(self::getCacheKey(static::CACHE_KEY_FIND, $id));
        static::clear(self::getCooperationCacheKey(static::CACHE_KEY_USER, $id));
    }
}
