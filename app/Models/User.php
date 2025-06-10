<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use App\Observers\UserObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Deprecation\DeprecationLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Helpers\HoomdossierSession;
use App\Traits\HasCooperationTrait;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;

/**
 * App\Models\User
 *
 * @property int $id
 * @property int|null $account_id
 * @property int|null $cooperation_id
 * @property string $first_name
 * @property string $last_name
 * @property string $phone_number
 * @property string|null $last_visited_url
 * @property array<array-key, mixed>|null $extra
 * @property bool $allow_access
 * @property \Illuminate\Support\Carbon|null $tool_last_changed_at
 * @property \Illuminate\Support\Carbon|null $regulations_refreshed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $refreshing_regulations
 * @property-read \App\Models\Account|null $account
 * @property-read \App\Models\Building|null $building
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BuildingCoachStatus> $buildingCoachStatuses
 * @property-read int|null $building_coach_statuses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BuildingNotes> $buildingNotes
 * @property-read int|null $building_notes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BuildingPermission> $buildingPermissions
 * @property-read int|null $building_permissions_count
 * @property-read \App\Models\CompletedQuestionnaire|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Questionnaire> $completedQuestionnaires
 * @property-read int|null $completed_questionnaires_count
 * @property-read \App\Models\Cooperation|null $cooperation
 * @property-read \App\Models\UserEnergyHabit|null $energyHabit
 * @property-read mixed $email
 * @property-read mixed $is_admin
 * @property-read mixed $old_email_token
 * @property-read mixed $oldemail
 * @property-read \App\Models\TFactory|null $use_factory
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Log> $logs
 * @property-read int|null $logs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\NotificationSetting> $notificationSettings
 * @property-read int|null $notification_settings_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserActionPlanAdviceComments> $userActionPlanAdviceComments
 * @property-read int|null $user_action_plan_advice_comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserActionPlanAdvice> $userActionPlanAdvices
 * @property-read int|null $user_action_plan_advices_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserCost> $userCosts
 * @property-read int|null $user_costs_count
 * @method static Builder<static>|User byContact($contact)
 * @method static Builder<static>|User econobisContacts()
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static Builder<static>|User forAllCooperations()
 * @method static Builder<static>|User forMyCooperation(\App\Models\Cooperation|int $cooperation)
 * @method static Builder<static>|User newModelQuery()
 * @method static Builder<static>|User newQuery()
 * @method static Builder<static>|User permission($permissions, $without = false)
 * @method static Builder<static>|User query()
 * @method static Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static Builder<static>|User whereAccountId($value)
 * @method static Builder<static>|User whereAllowAccess($value)
 * @method static Builder<static>|User whereCooperationId($value)
 * @method static Builder<static>|User whereCreatedAt($value)
 * @method static Builder<static>|User whereExtra($value)
 * @method static Builder<static>|User whereFirstName($value)
 * @method static Builder<static>|User whereId($value)
 * @method static Builder<static>|User whereLastName($value)
 * @method static Builder<static>|User whereLastVisitedUrl($value)
 * @method static Builder<static>|User wherePhoneNumber($value)
 * @method static Builder<static>|User whereRefreshingRegulations($value)
 * @method static Builder<static>|User whereRegulationsRefreshedAt($value)
 * @method static Builder<static>|User whereToolLastChangedAt($value)
 * @method static Builder<static>|User whereUpdatedAt($value)
 * @method static Builder<static>|User withoutPermission($permissions)
 * @method static Builder<static>|User withoutRole($roles, $guard = null)
 * @mixin \Eloquent
 */
#[ObservedBy([UserObserver::class])]
class User extends Model implements AuthorizableContract
{
    use HasFactory,
        HasRoles,
        HasCooperationTrait,
        Authorizable;

    protected $guard_name = 'web';

    protected $fillable = [
        'tool_last_changed_at', 'extra', 'first_name', 'last_name', 'phone_number',
        'account_id', 'allow_access', 'regulations_refreshed_at',
        'last_visited_url', 'cooperation_id',
    ];

    protected function casts(): array
    {
        return [
            'allow_access' => 'boolean',
            'extra' => 'array',
            'tool_last_changed_at' => 'datetime:Y-m-d H:i:s',
            'regulations_refreshed_at' => 'datetime:Y-m-d H:i:s',
        ];
    }

    // We can't eager load roles by default because if the admin changes them, they don't refresh
    //protected $with = [
    //    'roles',
    //];

    # Scopes
    #[Scope]
    protected function byContact(Builder $query, $contact): Builder
    {
        // We assume $contact is an ID. Maybe in the future this won't be the case but this way it can be easily
        // expanded
        return $query->where('extra->contact_id', $contact);
    }

    #[Scope]
    protected function econobisContacts(Builder $query): Builder
    {
        return $query->whereNotNull('extra->contact_id');
    }

    # Relations
    public function logs(): MorphMany
    {
        return $this->morphMany(Log::class, 'loggable');
    }

    public function considerables($related): MorphToMany
    {
        return $this->morphedByMany($related, 'considerable', 'considerables')
            ->withPivot(['is_considering', 'input_source_id']);
    }

    public function considerablesForModel(Model $related): MorphToMany
    {
        return $this->considerables($related->getMorphClass())->wherePivot('considerable_id', $related->id);
    }

    # Unsorted
    public function considers(Model $model, InputSource $inputSource): bool
    {
        $considerableModel = $this->considerablesForModel($model)
            ->wherePivot('input_source_id', $inputSource->id)
            ->first();

        if ($considerableModel instanceof Model) {
            return $considerableModel->pivot->is_considering;
        }
        // no considerable found ? We will return true
        // we do this so the Woonplan won't be left out empty.
        return true;
    }

    public function allowedAccess(): bool
    {
        return $this->allow_access;
    }

    // ------ User -> Account table / model migration stuff -------

    public function getEmailAttribute()
    {
        return $this->getAccountProperty('email');
    }

    public function getOldEmailTokenAttribute()
    {
        return $this->getAccountProperty('old_email_token');
    }

    public function getOldemailAttribute()
    {
        return $this->getAccountProperty('old_email');
    }

    public function getIsAdminAttribute()
    {
        return $this->getAccountProperty('is_admin');
    }

    /**
     * Quick short hand helper for user to account data migration.
     *
     *
     * @return mixed|null
     */
    public function getAccountProperty(string $property)
    {
        \Log::debug('Account property ' . $property . ' is accessed via User!');
        if ($this->account instanceof Account) {
            return $this->account->$property;
        }

        return null;
    }

    public function building(): HasOne
    {
        return $this->hasOne(Building::class);
    }

    public function buildingCoachStatuses(): HasMany
    {
        return $this->hasMany(BuildingCoachStatus::class, 'coach_id');
    }

    /**
     * Return the notification settings from a user.
     */
    public function notificationSettings(): HasMany
    {
        return $this->hasMany(NotificationSetting::class);
    }

    /**
     * Determine if a user retrieves a notification.
     *
     * @param $notificationTypeShort
     */
    public function retrievesNotifications($notificationTypeShort): bool
    {
        $notificationType = NotificationType::where('short', $notificationTypeShort)->first();
        $notInterestedInterval = NotificationInterval::where('short', 'no-interest')->first();

        return $this->notificationSettings()
            ->where('type_id', $notificationType->id)
            ->where('interval_id', '!=', $notInterestedInterval->id)
            ->exists();
    }

    public function energyHabit(): HasOne
    {
        return $this->hasOne(UserEnergyHabit::class);
    }

    /**
     * Return all the building notes a user has created.
     */
    public function buildingNotes(): HasMany
    {
        return $this->hasMany(BuildingNotes::class, 'coach_id', 'id');
    }

    public function userActionPlanAdvices(): HasMany
    {
        return $this->hasMany(UserActionPlanAdvice::class);
    }

    public function userActionPlanAdviceComments(): HasMany
    {
        return $this->hasMany(UserActionPlanAdviceComments::class);
    }

    public function userCosts(): HasMany
    {
        return $this->hasMany(UserCost::class);
    }

    /**
     * The cooperations the user is associated with.
     */
    public function cooperation(): BelongsTo
    {
        return $this->belongsTo(Cooperation::class, 'cooperation_id', 'id');
    }

    /**
     * Returns the first and last name, concatenated.
     */
    public function getFullName(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function buildingPermissions(): HasMany
    {
        return $this->hasMany(BuildingPermission::class);
    }

    /**
     * Check if a user is removed from the building coach status table.
     */
    public function isRemovedFromBuildingCoachStatus(int $buildingId): bool
    {
        // get the last known coach status for the current coach
        $buildingCoachStatus = BuildingCoachStatus::where('coach_id', $this->id)
            ->where('building_id', $buildingId)
            ->orderByDesc('id')
            ->first();

        if ($buildingCoachStatus instanceof BuildingCoachStatus) {
            // if the coach his last known building status for the current building is removed
            // we return true, the user either removed from the building
            if (BuildingCoachStatus::STATUS_REMOVED == $buildingCoachStatus->status) {
                return true;
            }
        }

        return false;
    }

    public function isNotRemovedFromBuildingCoachStatus(int $buildingId): bool
    {
        return ! $this->isRemovedFromBuildingCoachStatus($buildingId);
    }

    /**
     * Check if the logged in user is filling the tool for someone else.
     */
    public function isFillingToolForOtherBuilding(): bool
    {
        // if the building is not set it is null, so return false.
        // this will only happen in very rare occasions (prob only on dev / local)
//        if (is_null(HoomdossierSession::getBuilding())) {
//            return false;
//        } else {
        if ($this->building->id !== HoomdossierSession::getBuilding()) {
            return true;
        }

        return false;
//    }
    }

    /**
     * Determine if the model has not (one of) the given role(s).
     *
     * @param string|array|\Spatie\Permission\Contracts\Role|\Illuminate\Support\Collection $roles
     */
    public function hasNotRole($roles): bool
    {
        return ! $this->hasRole($roles);
    }

    /**
     * Check if a user has multiple roles.
     */
    public function hasMultipleRoles(): bool
    {
        if ($this->getRoleNames()->count() > 1) {
            return true;
        }

        return false;
    }

    /**
     * Function to check if a user has a role, and if the user has that role check if
     * the role is set in the Hoomdossier session.
     */
    public function hasRoleAndIsCurrentRole(string|array|Role|Collection $roles): bool
    {
        // collect the role names from the gives roles.
        $roleNames = [];
        if (is_string($roles) && str_contains($roles, '|')) {
            $roleNames = $this->convertPipeToArray($roles);
        }

        if (is_string($roles)) {
            $roleNames = [$roles];
        }

        if (is_array($roles)) {
            $roleNames = $roles;
        }

        if ($roles instanceof Role) {
            $roleNames = [$roles->name];
        }

        if ($roles instanceof Collection) {
            $this->hasRoleAndIsCurrentRole($roles->toArray());
        }

        // get the current role based on the session
        $currentRole = HoomdossierSession::getRole(true);

        // check if the user has the role, and if the current role is set in the role
        if (is_array($roleNames) && $this->hasRole($roles) && in_array($currentRole->name, $roleNames)) {
            return true;
        }

        return false;
    }

    /**
     * Check if the user has one role.
     */
    public function hasNotMultipleRoles(): bool
    {
        return ! $this->hasMultipleRoles();
    }

    /**
     * Retrieve the completed questionnaires from the user.
     */
    public function completedQuestionnaires(): BelongsToMany
    {
        return $this->belongsToMany(Questionnaire::class, 'completed_questionnaires')
            ->using(CompletedQuestionnaire::class);
    }

    /**
     * Check whether a user completed a questionnaire.
     */
    public function hasCompletedQuestionnaire(Questionnaire $questionnaire, InputSource $inputSource = null): bool
    {
        $query = $this->completedQuestionnaires()
            ->where('questionnaire_id', $questionnaire->id);

        if ($inputSource instanceof InputSource) {
            $query->wherePivot('input_source_id', $inputSource->id);
        }

        return $query->exists();
    }

    /**
     * Return the user its account information.
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
    /**

     * Get the user its email from the accounts table.
     */
    public function getEmailForPasswordReset(): string
    {
        return $this->account->email;
    }

    public function logout(): void
    {
        // used in the handler.php
        HoomdossierSession::destroy();
        Auth::logout();
        request()->session()->invalidate();
    }
}
