<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

/**
 * App\Models\Account.
 *
 * @property int                                                                                                       $id
 * @property string                                                                                                    $email
 * @property string                                                                                                    $password
 * @property string|null                                                                                               $remember_token
 * @property string|null                                                                                               $confirm_token
 * @property string|null                                                                                               $old_email
 * @property string|null                                                                                               $old_email_token
 * @property int                                                                                                       $active
 * @property bool                                                                                                      $is_admin
 * @property \Illuminate\Support\Carbon|null                                                                           $created_at
 * @property \Illuminate\Support\Carbon|null                                                                           $updated_at
 * @property \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\User[]                                               $users
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereConfirmToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereIsAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereOldEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereOldEmailToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Account extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'phone_number',
        'confirm_token', 'old_email', 'old_email_token',
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
        $this->notify(new ResetPasswordNotification($token, $this, $this->user()->cooperation));
    }

    /**
     * Return a collection of cooperations that belongto the users associated with the current account.
     *
     * @return Collection
     */
    public function cooperations(): Collection
    {
        /** @var Collection $users */
        $users = $this->users()->forAllCooperations()->with('cooperation')->get();
        $cooperations = $users->map(function ($user) {
            return $user->cooperation;
        });

        return $cooperations;
    }

    /**
     * Will return the user from the account and cooperation that is being used.
     *
     * This will work because the global cooperation scope is applied.
     *
     * @return User|null
     */
    public function user()
    {
        return \App\Helpers\Cache\Account::user($this);
    }

    /**
     * Will return all the users from the account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
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
        return $this->users()->withoutGlobalScopes()->where('cooperation_id', '=', $cooperation->id)->count() > 0;
    }
}
