<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Account extends Authenticatable
{
    use Notifiable;

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
     * Send the password reset notification.
     *
     * @param string $token
     *
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token, $this->user()->cooperation));
    }

    /**
     * Will return the user from the account and cooperation that is being used
     *
     * This will work because the global cooperation scope is applied.
     *
     * @return User|null
     */
    public function user()
    {
        return $this->users()->first();
    }

    /**
     * Will return all the users from the account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class)->withoutGlobalScopes();
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
        return $this->users()->withoutGlobalScopes()->where('cooperation_id', '=', $cooperation->id)->count() > 0;

    }
}