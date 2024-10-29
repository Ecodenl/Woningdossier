<?php

namespace App\Models;

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
 * @property array|null $extra
 * @property bool $allow_access
 * @property \Illuminate\Support\Carbon|null $tool_last_changed_at
 * @property \Illuminate\Support\Carbon|null $regulations_refreshed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $refreshing_regulations
 * @property-read \App\Models\Account|null $account
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserActionPlanAdvice> $actionPlanAdvices
 * @property-read int|null $action_plan_advices_count
 * @property-read \App\Models\Building|null $building
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BuildingCoachStatus> $buildingCoachStatuses
 * @property-read int|null $building_coach_statuses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BuildingNotes> $buildingNotes
 * @property-read int|null $building_notes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BuildingPermission> $buildingPermissions
 * @property-read int|null $building_permissions_count
 * @property-read \Plank\Mediable\MediableCollection<int, \App\Models\Building> $buildings
 * @property-read int|null $buildings_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Questionnaire> $completedQuestionnaires
 * @property-read int|null $completed_questionnaires_count
 * @property-read \App\Models\Cooperation|null $cooperation
 * @property-read \Plank\Mediable\MediableCollection<int, \App\Models\Cooperation> $cooperations
 * @property-read int|null $cooperations_count
 * @property-read \App\Models\UserEnergyHabit|null $energyHabit
 * @property-read mixed $email
 * @property-read mixed $is_admin
 * @property-read mixed $old_email_token
 * @property-read mixed $oldemail
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Interest> $interests
 * @property-read int|null $interests_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Log> $logs
 * @property-read int|null $logs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserMotivation> $motivations
 * @property-read int|null $motivations_count
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserInterest> $userInterests
 * @property-read int|null $user_interests_count
 * @method static Builder|User byContact($contact)
 * @method static Builder|User econobisContacts()
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static Builder|User forAllCooperations()
 * @method static Builder|User forMyCooperation($cooperationId)
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User permission($permissions)
 * @method static Builder|User query()
 * @method static Builder|User role($roles, $guard = null)
 * @method static Builder|User whereAccountId($value)
 * @method static Builder|User whereAllowAccess($value)
 * @method static Builder|User whereCooperationId($value)
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereExtra($value)
 * @method static Builder|User whereFirstName($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereLastName($value)
 * @method static Builder|User whereLastVisitedUrl($value)
 * @method static Builder|User wherePhoneNumber($value)
 * @method static Builder|User whereRefreshingRegulations($value)
 * @method static Builder|User whereRegulationsRefreshedAt($value)
 * @method static Builder|User whereToolLastChangedAt($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class User extends Model implements AuthorizableContract
{
    use HasFactory;

    use HasRoles,
        HasCooperationTrait,
        Authorizable;

    protected $guard_name = 'web';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tool_last_changed_at', 'extra', 'first_name', 'last_name', 'phone_number', 'account_id', 'allow_access', 'regulations_refreshed_at',
        'last_visited_url',
    ];

    protected $casts = [
        'allow_access' => 'boolean',
        'extra' => 'array',
        'tool_last_changed_at' => 'datetime:Y-m-d H:i:s',
        'regulations_refreshed_at' => 'datetime:Y-m-d H:i:s',
    ];

    // We can't eager load roles by default because if the admin changes them, they don't refresh
    //protected $with = [
    //    'roles',
    //];

    # Scopes
    public function scopeByContact(Builder $query, $contact): Builder
    {
        // We assume $contact is an ID. Maybe in the future this won't be the case but this way it can be easily
        // expanded
        return $query->where('extra->contact_id', $contact);
    }

    public function scopeEconobisContacts(Builder $query): Builder
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
        $considerableModel =  $this->considerablesForModel($model)
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

    /**
     * Return the intermediary table of the interests.
     */
    public function userInterests(): HasMany
    {
        return $this->hasMany(UserInterest::class);
    }

    /**
     * Scope like method because of relationships.
     *
     * @param $interestedInType
     * @param $interestedInId
     */
    public function userInterestsForSpecificType($interestedInType, $interestedInId, InputSource $inputSource = null): HasMany
    {
        if ($inputSource instanceof InputSource) {
            return $this->userInterests()
                ->where('interested_in_type', $interestedInType)
                ->where('interested_in_id', $interestedInId)
                ->forInputSource($inputSource);
        }

        return $this->userInterests()
            ->where('interested_in_type', $interestedInType)
            ->where('interested_in_id', $interestedInId);
    }


    /**
     * Return all the interest levels of a user.
     */
    public function interests(): HasManyThrough
    {
        return $this->hasManyThrough(Interest::class, UserInterest::class, 'user_id', 'id', 'id', 'interest_id');
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

    // ------ End User -> Account table / model migration stuff -------
    public function buildings(): HasMany
    {
        // TODO: No user has more than one building.
        DeprecationLogger::log(__METHOD__ . ' really shouldn\'t be used anymore...');
        return $this->hasMany(Building::class);
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

        $doesUserRetrievesNotifications =

            $this->notificationSettings()
                ->where('type_id', $notificationType->id)
                ->where('interval_id', '!=', $notInterestedInterval->id)
                ->exists();

        return $doesUserRetrievesNotifications;
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

    public function motivations(): HasMany
    {
        return $this->hasMany(UserMotivation::class);
    }

    /**
     * @deprecated use userActionPlanAdvices
     */
    public function actionPlanAdvices(): HasMany
    {
        return $this->hasMany(UserActionPlanAdvice::class);
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
    public function cooperations(): BelongsToMany
    {
        return $this->belongsToMany(Cooperation::class, 'cooperation_user');
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

    public function isBuildingOwner(Building $building)
    {
        if ($this->buildings()->find($building->id) instanceof Building) {
            return true;
        }

        return false;
    }

    /**
     * Check if a user is not removed from the building coach status table.
     *
     * @param $buildingId
     */
    public function isRemovedFromBuildingCoachStatus($buildingId): bool
    {
        // get the last known coach status for the current coach
        $buildingCoachStatus = BuildingCoachStatus::where('coach_id', $this->id)->where('building_id', $buildingId)->get()->last();

        if ($buildingCoachStatus instanceof BuildingCoachStatus) {
            // if the coach his last known building status for the current building is removed
            // we return true, the user either removed from the building
            if (BuildingCoachStatus::STATUS_REMOVED == $buildingCoachStatus->status) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return the opposite of the isRemovedFromBuildingCoachStatus function.
     *
     * @param $buildingId
     */
    public function isNotRemovedFromBuildingCoachStatus($buildingId): bool
    {
        return !$this->isRemovedFromBuildingCoachStatus($buildingId);
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
        if ($this->building->id != HoomdossierSession::getBuilding()) {
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
        return !$this->hasRole($roles);
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
     * Function to check if a user has a role, and if the user has that role check if the role is set in the Hoomdossier session.
     *
     * @param string|array|\Spatie\Permission\Contracts\Role|\Illuminate\Support\Collection $roles
     */
    public function hasRoleAndIsCurrentRole($roles): bool
    {
        // collect the role names from the gives roles.
        $roleNames = [];
        if (is_string($roles) && false !== strpos($roles, '|')) {
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
        return !$this->hasMultipleRoles();
    }

    /**
     * Retrieve the completed questionnaires from the user.
     *
     * @param InputSource $inputSource
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
     * Get the user its bcrypted password from the accounts table.
     */
    public function getAuthPassword(): string
    {
        return $this->account->password;
    }

    /**
     * Get the user its email from the accounts table.
     */
    public function getEmailForPasswordReset(): string
    {
        return $this->account->email;
    }

    public function logout()
    {
        // used in the handler.php
        HoomdossierSession::destroy();
        \Auth::logout();
        request()->session()->invalidate();
    }
}
