<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
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
 * @property int|null                                                                                                  $notifications_count
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\User[]                                               $users
 * @property int|null                                                                                                  $users_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Account newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Account newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Account query()
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereConfirmToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereIsAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereOldEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereOldEmailToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Account extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'phone_number',
        'email_verified_at', 'old_email', 'old_email_token',
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
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailNotification($this->user()));
    }

    /**
     * Return a collection of cooperations that belongto the users associated with the current account.
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
     * @return bool
     */
    public function isAssociatedWith(Cooperation $cooperation)
    {
        return $this->users()->withoutGlobalScopes()->where('cooperation_id', '=', $cooperation->id)->count() > 0;
    }
}
