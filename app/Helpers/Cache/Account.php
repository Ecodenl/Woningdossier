<?php

namespace App\Helpers\Cache;

use App\Helpers\HoomdossierSession;
use App\Models\User;
use RuntimeException;
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
        // The user is found using the cooperation scope, which checks the session.
        // If it's not set, it won't work. And we also need it for the cache key.
        $cooperation = HoomdossierSession::getCooperation(true);
        throw_if(! $cooperation instanceof \App\Models\Cooperation, new RuntimeException('Cooperation NOT set!'));

        return Cache::remember(
            self::getCooperationCacheKey($cooperation, static::CACHE_KEY_USER, $account->id),
            config('hoomdossier.cache.times.default'),
            function () use ($account) {
                return $account->users()->first();
            }
        );
    }

    public static function wipe(AccountModel $account): void
    {
        static::clear(self::getCacheKey(static::CACHE_KEY_FIND, $account->id));

        // Clearing might be done in the queue, which might not have a session.
        // In that case, we will just update them all.
        foreach ($account->users()->forAllCooperations()->withWhereHas('cooperation')->get() as $user) {
            static::clear(self::getCooperationCacheKey($user->cooperation, static::CACHE_KEY_USER, $account->id));
        }
    }
}
