<?php

namespace App\Models;

use App\Observers\AccountObserver;
use App\Services\SmartTwin\Api\UserRole;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Fortify\TwoFactorAuthenticatable;

/**
 * App\Models\Account
 *
 * @property int $id
 * @property string $email
 * @property array<array-key, mixed>|null $extra
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property string|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $old_email
 * @property string|null $old_email_token
 * @property int $active
 * @property bool $is_admin
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\AccountFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account whereExtra($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account whereIsAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account whereOldEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account whereOldEmailToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account whereTwoFactorConfirmedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account whereTwoFactorRecoveryCodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account whereTwoFactorSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account whereUpdatedAt($value)
 * @mixin \Eloquent
 */
#[ObservedBy([AccountObserver::class])]
class Account extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    // Keys used inside the `extra` JSON column. SmartTwin identifies a user by e-mail address, which
    // belongs to the account, so the link belongs here rather than on the (per-cooperation) user.
    public const string EXTRA_SMARTTWIN_USER_ID = 'smarttwin_user_id';

    public const string EXTRA_SMARTTWIN_USER_ROLE = 'smarttwin_user_role';

    protected $fillable = ['email', 'password', 'email_verified_at', 'old_email', 'old_email_token',
        'extra',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_admin' => 'boolean',
            'email_verified_at' => 'datetime',
            'extra' => 'array',
        ];
    }

    /**
     * Send the password reset notification.
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token, $this, $this->user()->cooperation));
    }

    /**
     * Send the email verification notification.
     */
    public function sendEmailVerificationNotification(): void
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
     */
    public function user(): ?User
    {
        return \App\Helpers\Cache\Account::user($this);
    }

    /**
     * Will return all the users from the account.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * The SmartTwin user linked to this account, if one has been created.
     */
    public function smartTwinUserId(): ?string
    {
        return $this->extra[self::EXTRA_SMARTTWIN_USER_ID] ?? null;
    }

    /**
     * The role we created that SmartTwin user with. SmartTwin has no endpoint to change it
     * afterwards, so this can drift from the roles the account currently holds.
     */
    public function smartTwinUserRole(): ?UserRole
    {
        return UserRole::tryFrom($this->extra[self::EXTRA_SMARTTWIN_USER_ROLE] ?? -1);
    }

    public function linkSmartTwinUser(string $userId, UserRole $role): void
    {
        $this->extra = array_merge($this->extra ?? [], [
            self::EXTRA_SMARTTWIN_USER_ID   => $userId,
            self::EXTRA_SMARTTWIN_USER_ROLE => $role->value,
        ]);

        $this->save();
    }

    /**
     * Returns whether or not a user is associated with a particular Cooperation.
     */
    public function isAssociatedWith(Cooperation $cooperation): bool
    {
        return $this->users()->withoutGlobalScopes()->where('cooperation_id', '=', $cooperation->id)->count() > 0;
    }
}
