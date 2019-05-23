<?php

namespace App\Models;

use App\Helpers\HoomdossierSession;
use App\Notifications\ResetPasswordNotification;
use App\NotificationSetting;
use App\Scopes\GetValueScope;
use App\Traits\HasRolesTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

/**
 * App\Models\User.
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $password
 * @property string|null $remember_token
 * @property string|null $confirm_token
 * @property string $phone_number
 * @property string $mobile
 * @property string|null $last_visit
 * @property int $visit_count
 * @property int $active
 * @property bool $is_admin
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\UserActionPlanAdvice[] $actionPlanAdvices
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingNotes[] $buildingNotes
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingPermission[] $buildingPermissions
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingUserUsage[] $buildingUsage
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Building[] $buildings
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Questionnaire[] $completedQuestionnaires
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Cooperation[] $cooperations
 * @property \App\Models\UserEnergyHabit $energyHabit
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\UserInterest[] $interests
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\UserMotivation[] $motivations
 * @property \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Permission[] $permissions
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Role[] $roles
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User role($roles)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereConfirmToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereIsAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereLastVisit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereVisitCount($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use Notifiable;
    use HasRolesTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
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

    public function buildings()
    {
        return $this->hasMany(Building::class);
    }

    /**
     * Return the notification settings from a user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notificationSettings()
    {
        return $this->hasMany(NotificationSetting::class);
    }

    public function buildingUsage()
    {
        return $this->hasMany(BuildingUserUsage::class);
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

    /*
    public function progress()
    {
        return $this->hasMany(UserProgress::class);
    }
    */

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
        return $this->belongsToMany(Cooperation::class);
    }

    /**
     * Returns the interests off a user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function interests()
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
     * @return \Illuminate\Database\Eloquent\Model|null|object|static
     */
    public function getInterestedType($type, $interestedInId, InputSource $inputSource = null)
    {
        if ($inputSource instanceof InputSource) {
            return $this
                ->interests()
                ->withoutGlobalScope(GetValueScope::class)
                ->where('input_source_id', $inputSource->id)
                ->where('interested_in_type', $type)
                ->where('interested_in_id', $interestedInId)->first();
        }

        return $this->interests()->where('interested_in_type', $type)->where('interested_in_id', $interestedInId)->first();
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
        return $this->cooperations()
                    ->where('id', $cooperation->id)
                    ->count() > 0;
    }

    public function complete(Step $step)
    {
        \Log::debug(__METHOD__.' is still being used, this should not be');

        return UserProgress::firstOrCreate([
            'step_id' => $step->id,
            'input_source_id' => HoomdossierSession::getInputSource(),
            'building_id' => HoomdossierSession::getBuilding(),
        ]);
    }

    /**
     * Returns whether or not a user has completed a particular step.
     *
     * @param Step $step
     *
     * @return bool
     */
    public function hasCompleted(Step $step)
    {
        \Log::debug(__METHOD__.'is still being used somewhere, this should not be');

        return true;
    }

    /**
     * Send the password reset notification.
     *
     * @param string $token
     *
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token, $this->cooperations()->first()));
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

    /**
     * Check if a user had permissions for a specific building.
     *
     * @param $buildingId
     *
     * @return bool
     */
    public function hasBuildingPermission($buildingId): bool
    {
        if ($this->buildingPermissions()->where('building_id', $buildingId)->first() instanceof BuildingPermission) {
            return true;
        }

        return false;
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
            if ($this->buildings()->first()->id != HoomdossierSession::getBuilding()) {
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
    public function hasMultipleRoles($cooperationId = null): bool
    {
        if ($this->getRoleNames($cooperationId)->count() > 1) {
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
        $currentRole = Role::find(HoomdossierSession::getRole());

        // check if the user has the role, and if the current role is set in the role
        if ($this->hasRole($roles) && in_array($currentRole->name, $roleNames)) {
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
        $this->completedQuestionnaires()->syncWithoutDetaching($questionnaire);
    }

    /**
     * Check if a user gave permission to let cooperations access his building.
     *
     * @param $buildingId
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

    public function logout()
    {
        HoomdossierSession::destroy();
        \Auth::logout();
        request()->session()->invalidate();
    }
}
