<?php

namespace App\Models;

use App\Helpers\HoomdossierSession;
use App\Traits\HasCooperationTrait;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Database\Eloquent\Model;
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
 * @property bool $allow_access
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Account|null $account
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserActionPlanAdvice[] $actionPlanAdvices
 * @property-read int|null $action_plan_advices_count
 * @property-read \App\Models\Building|null $building
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingNotes[] $buildingNotes
 * @property-read int|null $building_notes_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingPermission[] $buildingPermissions
 * @property-read int|null $building_permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Building[] $buildings
 * @property-read int|null $buildings_count
 * @property-read \App\Models\Cooperation|null $cooperation
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Cooperation[] $cooperations
 * @property-read int|null $cooperations_count
 * @property-read \App\Models\UserEnergyHabit|null $energyHabit
 * @property-read mixed $email
 * @property-read mixed $is_admin
 * @property-read mixed $old_email_token
 * @property-read mixed $oldemail
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Interest[] $interests
 * @property-read int|null $interests_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\MeasureApplication[] $measureApplicationInterest
 * @property-read int|null $measure_application_interest_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserMotivation[] $motivations
 * @property-read int|null $motivations_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\NotificationSetting[] $notificationSettings
 * @property-read int|null $notification_settings_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Permission[] $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Role[] $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Step[] $stepInterests
 * @property-read int|null $step_interests_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserActionPlanAdviceComments[] $userActionPlanAdviceComments
 * @property-read int|null $user_action_plan_advice_comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserInterest[] $userInterests
 * @property-read int|null $user_interests_count
 * @method static \Illuminate\Database\Eloquent\Builder|User forAllCooperations()
 * @method static \Illuminate\Database\Eloquent\Builder|User forMyCooperation($cooperationId)
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAllowAccess($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCooperationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class User extends Model implements AuthorizableContract
{
    use HasRoles;
    use HasCooperationTrait;
    use Authorizable;

    protected $guard_name = 'web';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
      'extra', 'first_name', 'last_name', 'phone_number', 'account_id', 'allow_access',
    ];

    protected $casts = [
        'allow_access' => 'boolean',
        'extra' => 'array'
    ];

    public function allowedAccess(): bool
    {
        return $this->allow_access;
    }

    /**
     * Return the intermediary table of the interests.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userInterests()
    {
        return $this->hasMany(UserInterest::class);
    }

    /**
     * Scope like method because of relationships.
     *
     * @param $interestedInType
     * @param $interestedInId
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userInterestsForSpecificType($interestedInType, $interestedInId, InputSource $inputSource = null)
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
     * Method to check whether a user is interested in a step.
     *
     * @param $interestedInType
     * @param $interestedInId
     *
     * @return bool
     */
    public function isInterestedInStep(InputSource $inputSource, $interestedInType, $interestedInId)
    {
        $noInterestIds = Interest::whereIn('calculate_value', [4, 5])->select('id')->get()->pluck('id')->toArray();

        $userSelectedInterestedId = $this->user->userInterestsForSpecificType($interestedInType, $interestedInId)->first()->interest_id;

        return ! in_array($userSelectedInterestedId, $noInterestIds);
    }

    /**
     * Return all the interest levels of a user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function interests()
    {
        return $this->hasManyThrough(Interest::class, UserInterest::class, 'user_id', 'id', 'id', 'interest_id');
    }

    /**
     * Return all step interests.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function stepInterests()
    {
        return $this->morphedByMany(Step::class, 'interested_in', 'user_interests')
            ->where('user_interests.input_source_id', HoomdossierSession::getInputSourceValue())
            ->withPivot('interest_id', 'input_source_id');
    }

    /**
     * Return all the measure application interests.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function measureApplicationInterest()
    {
        return $this->morphedByMany(MeasureApplication::class, 'interested_in', 'user_interests')
            ->where('user_interests.input_source_id', HoomdossierSession::getInputSourceValue())
            ->withPivot('interest_id', 'input_source_id');
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
     * @param string $property
     *
     * @return mixed|null
     */
    public function getAccountProperty($property)
    {
        \Log::debug('Account property '.$property.' is accessed via User!');
        if ($this->account instanceof Account) {
            return $this->account->$property;
        }

        return null;
    }

    // ------ End User -> Account table / model migration stuff -------
    public function buildings()
    {
        return $this->hasMany(Building::class);
    }

    public function building()
    {
        return $this->hasOne(Building::class);
    }

    /**
     * Return the notification settings from a user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notificationSettings()
    {
        return $this->hasMany(NotificationSetting::class);
    }

    /**
     * Determine if a user retrieves a notification.
     *
     * @param $notificationTypeShort
     *
     * @return bool
     */
    public function retrievesNotifications($notificationTypeShort)
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

    public function energyHabit()
    {
        return $this->hasOne(UserEnergyHabit::class);
    }

    /**
     * Return all the building notes a user has created.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function buildingNotes()
    {
        return $this->hasMany(BuildingNotes::class, 'coach_id', 'id');
    }

    public function userActionPlanAdviceComments()
    {
        return $this->hasMany(UserActionPlanAdviceComments::class);
    }

    public function motivations()
    {
        return $this->hasMany(UserMotivation::class);
    }

    public function actionPlanAdvices()
    {
        return $this->hasMany(UserActionPlanAdvice::class);
    }

    /**
     * The cooperations the user is associated with.
     */
    public function cooperations()
    {
        return $this->belongsToMany(Cooperation::class, 'cooperation_user');
    }

    /**
     * The cooperations the user is associated with.
     */
    public function cooperation()
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

    /**
     * Returns if a user has interest in a specific model (mostly Step or
     * MeasureApplication).
     *
     * @return bool
     */
    public function hasInterestIn(Model $model, InputSource $inputSource = null, int $interestCalculateValue = 2)
    {
        $userInterests = $this->userInterestsForSpecificType(get_class($model), $model->id, $inputSource)->with('interest')->get();
        foreach ($userInterests as $userInterest) {
            // the $interestCalculateValue is default 2, but due to some exceptions in the app this may be variable.
            if ($userInterest->interest->calculate_value <= $interestCalculateValue) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns a specific interested row for a specific type.
     *
     * @param $type
     * @param $interestedInId
     *
     * @return UserInterest
     */
    public function getInterestedType($type, $interestedInId, InputSource $inputSource = null)
    {
        if ($inputSource instanceof InputSource) {
            return $this
                ->interests()
                ->forInputSource($inputSource)
                ->where('interested_in_type', $type)
                ->where('interested_in_id', $interestedInId)->first();
        }

        return $this->interests()->where('interested_in_type', $type)->where('interested_in_id', $interestedInId)->first();
    }

    /**
     * Get the human readable role name based on the role name.
     *
     * @param $roleName
     *
     * @return mixed
     */
    public function getHumanReadableRoleName($roleName)
    {
        return $this->roles()->where('name', $roleName)->first()->human_readable_name;
    }

    public function buildingPermissions()
    {
        return $this->hasMany(\App\Models\BuildingPermission::class);
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
        return ! $this->hasMultipleRoles();
    }

    /**
     * Retrieve the completed questionnaires from the user.
     *
     * @param InputSource $inputSource
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function completedQuestionnaires(InputSource $inputSource = null)
    {
        // global scopes wont work on the intermediary table
        $inputSource = is_null($inputSource) ? HoomdossierSession::getInputSource(true) : $inputSource;

        return $this->belongsToMany(Questionnaire::class, 'completed_questionnaires')
            ->wherePivot('input_source_id', $inputSource->id);
    }

    /**
     * Check whether a user completed a questionnaire.
     *
     * @return bool
     */
    public function hasCompletedQuestionnaire(Questionnaire $questionnaire, InputSource $inputSource = null)
    {
        if ($inputSource instanceof InputSource) {
            return $this
                ->completedQuestionnaires($inputSource)
                ->where('questionnaire_id', $questionnaire->id)
                ->exists();
        }

        return $this->completedQuestionnaires()
            ->where('questionnaire_id', $questionnaire->id)
            ->exists();
    }

    /**
     * Complete a questionnaire for a user.
     *
     * @param InputSource $inputSource
     */
    public function completeQuestionnaire(Questionnaire $questionnaire, InputSource $inputSource = null)
    {
        $inputSource = is_null($inputSource) ? HoomdossierSession::getInputSource(true) : $inputSource;

        $this->completedQuestionnaires()->syncWithoutDetaching(/* @scrutinizer ignore-type, uses parseIds method. */
            [
                $questionnaire->id => [
                    'input_source_id' => $inputSource->id,
                ],
            ]
        );
    }

    /**
     * Return the user its account information.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get the user its bcrypted password from the accounts table.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->account->password;
    }

    /**
     * Get the user its email from the accounts table.
     *
     * @return string
     */
    public function getEmailForPasswordReset()
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
