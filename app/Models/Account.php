<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;

class Account extends Authenticatable
{

    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'phone_number',
        'confirm_token', 'old_email', 'old_email_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_admin' => 'boolean',
    ];

    /**
     *
     *
     * @return User|null
     */
    public function user()
    {
        return $this->users()->first();
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Returns whether or not a user is associated with a particular Cooperation.
     *
     * @param Cooperation $cooperation
     *
     * @return bool
     */
    public function isAssociatedWith(Cooperation $cooperation)
    {
        return Account::whereHas('users', function($query) use($cooperation){
            $query->where('cooperation_id', '=', $cooperation->id);
        })->count() > 0;

    }


}