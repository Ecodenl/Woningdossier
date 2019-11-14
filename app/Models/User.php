<?php

namespace App\Models;

use App\Helpers\HoomdossierSession;
use App\NotificationSetting;
use App\Traits\HasCooperationTrait;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Support\Collection;
use Spatie\Permission\Traits\HasRoles;

/**
 * App\Models\User.
 *
 * @property int                                                                                 $id
 * @property int|null                                                                            $account_id
 * @property int|null                                                                            $cooperation_id
 * @property string                                                                              $first_name
 * @property string                                                                              $last_name
 * @property string                                                                              $phone_number
 * @property \Illuminate\Support\Carbon|null                                                     $created_at
 * @property \Illuminate\Support\Carbon|null                                                     $updated_at
 * @property \App\Models\Account|null                                                            $account
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\UserActionPlanAdvice[]         $actionPlanAdvices
 * @property \App\Models\Building                                                                $building
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingNotes[]                $buildingNotes
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingPermission[]           $buildingPermissions
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Building[]                     $buildings
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Questionnaire[]                $completedQuestionnaires
 * @property \App\Models\Cooperation|null                                                        $cooperation
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Cooperation[]                  $cooperations
 * @property \App\Models\UserEnergyHabit                                                         $energyHabit
 * @property mixed                                                                               $email
 * @property mixed                                                                               $is_admin
 * @property mixed                                                                               $old_email_token
 * @property mixed                                                                               $oldemail
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\UserInterest[]                 $interests
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\UserMotivation[]               $motivations
 * @property \Illuminate\Database\Eloquent\Collection|\App\NotificationSetting[]                 $notificationSettings
 * @property \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Permission[]     $permissions
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Role[]                         $roles
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\UserActionPlanAdviceComments[] $userActionPlanAdviceComments
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User forMyCooperation($cooperationId)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCooperationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class User extends Model implements AuthorizableContract
{
    use HasRoles;
    use HasCooperationTrait;
    use Authorizable;

    protected $guard_name = 'web';


    /**
     * Return the intermediary table of the interests
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userInterests()
    {
        return $this->hasMany(UserInterest::class);
    }

    /**
     * Scope like method because of relationships
     *
     * @param $interestedInType
     * @param $interestedInId
     * @param InputSource|null $inputSource
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
     * Method to check whether a user is interested in a step
     *
     * @param InputSource $inputSource
     * @param $interestedInType
     * @param $interestedInId
     * @return bool
     */
    public function isInterestedInStep(InputSource $inputSource, $interestedInType, $interestedInId)
    {
        $noInterestIds = Interest::whereIn('calculate_value', [4, 5])->select('id')->get()->pluck('id')->toArray();

        $userSelectedInterestedId = $this->user->userInterestsForSpecificType($interestedInType, $interestedInId)->first()->interest_id;

        return !in_array($userSelectedInterestedId, $noInterestIds);
    }
    /**
     * Return all the interest levels of a user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function interests()
    {
        return $this->hasManyThrough(Interest::class, UserInterest::class, 'user_id', 'id', 'id', 'interest_id');
    }

    /**
     * Return all step interests
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
     * Return all the measure application interests
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function measureApplicationInterest()
    {
        return $this->morphedByMany(MeasureApplication::class, 'interested_in', 'user_interests')
            ->where('user_interests.input_source_id', HoomdossierSession::getInputSourceValue())
            ->withPivot('interest_id', 'input_source_id');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'phone_number',
    ];

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
     * Returns the interests off a user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function interestsv2()
    {
        return $this->hasMany(UserInterest::class);
    }

    /**
     * Returns the first and last name, concatenated.
     *
     * @return string
     */
    public function getFullName(): string
    {
        return "{$this->first_name} {$this->last_name}";
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
                ->userInterests()
                ->forInputSource($inputSource)
                ->where('interested_in_type', $type)
                ->where('interested_in_id', $interestedInId)->first();
        }

        return $this->userInterests()->where('interested_in_type', $type)->where('interested_in_id', $interestedInId)->first();
    }

    public function complete(Step $step)
    {
        \Log::debug(__METHOD__.' is still being used, this should not be');

        return CompletedStep::firstOrCreate([
            'step_id' => $step->id,
            'input_source_id' => HoomdossierSession::getInputSource(),
            'building_id' => HoomdossierSession::getBuilding(),
        ]);
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
        return $this->hasMany('App\Models\BuildingPermission');
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
     *
     * @return bool
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
     *
     * @return bool
     */
    public function isNotRemovedFromBuildingCoachStatus($buildingId): bool
    {
        return ! $this->isRemovedFromBuildingCoachStatus($buildingId);
    }

    /**
     * Check if the logged in user is filling the tool for someone else.
     *
     * @return bool
     */
    public function isFillingToolForOtherBuilding(): bool
    {
        // if the building is not set it is null, so return false.
        // this will only happen in very rare occasions (prob only on dev / local)
        if (is_null(HoomdossierSession::getBuilding())) {
            return false;
        } else {
            if ($this->building->id != HoomdossierSession::getBuilding()) {
                return true;
            }

            return false;
        }
    }

    /**
     * Determine if the model has not (one of) the given role(s).
     *
     * @param string|array|\Spatie\Permission\Contracts\Role|\Illuminate\Support\Collection $roles
     *
     * @return bool
     */
    public function hasNotRole($roles): bool
    {
        return ! $this->hasRole($roles);
    }

    /**
     * Check if a user has multiple roles.
     *
     * @return bool
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
     *
     * @return bool
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
     *
     * @return bool
     */
    public function hasNotMultipleRoles(): bool
    {
        return ! $this->hasMultipleRoles();
    }

    public function completedQuestionnaires()
    {
        return $this->belongsToMany(Questionnaire::class, 'completed_questionnaires');
    }

    /**
     * Complete a questionnaire for a user.
     *
     * @param Questionnaire $questionnaire
     */
    public function completeQuestionnaire(Questionnaire $questionnaire)
    {
        $this->completedQuestionnaires()->syncWithoutDetaching(/* @scrutinizer ignore-type, uses parseIds method. */ $questionnaire);
    }

    /**
     * Check if a user gave permission to let cooperations access his building.
     *
     * @param $buildingId
     *
     * @return bool
     */
    public function allowedAccessToHisBuilding($buildingId)
    {
        $conversationRequest = PrivateMessage::conversationRequestByBuildingId($buildingId)->first();

        if ($conversationRequest instanceof PrivateMessage && $conversationRequest->allow_access) {
            return true;
        }

        return false;
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
        HoomdossierSession::destroy();
        \Auth::logout();
        request()->session()->invalidate();
    }
}
