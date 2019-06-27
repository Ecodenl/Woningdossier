<?php

namespace App\Extensions;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Support\Str;

class AccountUserProvider extends EloquentUserProvider {
//
//    /**
//     * Retrieve a user by its given credentials.
//     *
//     * @param  array  $credentials
//     *
//     * @return UserContract|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|void|null
//     */
//    public function retrieveByCredentials(array $credentials)
//    {
//        if (empty($credentials) || (count($credentials) === 1 && array_key_exists('password', $credentials))) {
//            return;
//        }
//
//        // First we will add each credential element to the query as a where clause.
//        // Then we can execute the query and, if we found a user, return it in a
//        // Eloquent User "model" that will be utilized by the Guard instances.
//        $query = $this->createModel()->newQuery();
//
//        foreach ($credentials as $key => $value) {
//            if (! Str::contains($key, 'password')) {
//                $query->whereHas('account')->with(['account' => function ($query) use ($key, $value){
//                    $query->where($key, $value);
//                }]);
//            }
//        }
//
//
//        return $query->first();
//
//    }
//
//    /**
//     * Validate the credentials of the user.
//     *
//     * @param  UserContract  $user
//     * @param  array  $credentials
//     *
//     * @return bool
//     */
//    public function validateCredentials(UserContract $user, array $credentials)
//    {
//        $plain = $credentials['password'];
//
//        return $this->hasher->check($plain, $user->getAuthPassword());
//    }
}
