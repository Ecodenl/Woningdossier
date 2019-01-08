<?php

namespace App\Models;

use App\Helpers\HoomdossierSession;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

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
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\UserActionPlanAdvice[] $actionPlanAdvices
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingUserUsage[] $buildingUsage
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Building[] $buildings
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\UserProgress[] $completedSteps
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Cooperation[] $cooperations
 * @property \App\Models\UserEnergyHabit $energyHabit
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\UserInterest[] $interests
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\UserMotivation[] $motivations
 * @property \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Permission[] $permissions
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\UserProgress[] $progress
 * @property \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Role[] $roles
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User permission($permissions)
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
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'phone_number',
        'confirm_token',
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

    public function progress()
    {
        return $this->hasMany(UserProgress::class);
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
     * Returns the user progress.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function completedSteps()
    {
        return $this->hasMany(UserProgress::class);
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
     * Returns a specific interested row for a specific type.
     *
     * @param $type
     * @param $interestedInId
     *
     * @return \Illuminate\Database\Eloquent\Model|null|object|static
     */
    public function getInterestedType($type, $interestedInId)
    {
        return $this->interests()->where('interested_in_type', $type)->where('interested_in_id', $interestedInId)->first();
    }

    /**
     * check if a user is interested in a step.
     *
     * @param $type
     * @param array $interestedInIds
     *
     * @return bool
     */
    public function isNotInterestedInStep($type, $interestedInIds = [])
    {
        // the interest ids that people select when they do not have any interest
        $noInterestIds = [4, 5];

        $interestedIds = [];

        if (! is_array($interestedInIds)) {
            $interestedInIds = [$interestedInIds];
        }

        // go through the elementid and get the user interest id to put them into the array
        foreach ($interestedInIds as $key => $interestedInId) {
            if ($this->getInterestedType($type, $interestedInId) instanceof UserInterest) {
                array_push($interestedIds, $this->getInterestedType($type, $interestedInId)->interest_id);
            }
        }

        // check if the user wants to do something with their glazing
        if ($interestedIds == array_intersect($interestedIds, $noInterestIds) && $this->getInterestedType($type, $interestedInId) instanceof UserInterest) {
            return true;
        }

        return false;
    }

    public function isInterestedInStep($type, $interestedInIds = [])
    {
        // the interest ids that people select when they do not have any interest
        $noInterestIds = [4, 5];

        $interestedIds = [];

        if (! is_array($interestedInIds)) {
            $interestedInIds = [$interestedInIds];
        }

        // go through the elementid and get the user interest id to put them into the array
        foreach ($interestedInIds as $key => $interestedInId) {
            if ($this->getInterestedType($type, $interestedInId) instanceof UserInterest) {
                array_push($interestedIds, $this->getInterestedType($type, $interestedInId)->interest_id);
            }
        }

        // check if the user wants to do something with their glazing
        if ($interestedIds == array_intersect($interestedIds, $noInterestIds) && $this->getInterestedType($type, $interestedInId) instanceof UserInterest) {
            return false;
        }

        return true;
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
        return UserProgress::firstOrCreate([
            'step_id' => $step->id,
            'user_id' => \Auth::user()->id,
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
        return $this->completedSteps()->where('step_id', $step->id)->where('building_id', HoomdossierSession::getBuilding())->count() > 0;
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
     * Check if the logged in user is filling the tool for someone else.
     *
     * @return bool
     */
    public function isFillingToolForOtherBuilding(): bool
    {
        if ($this->buildings()->first()->id != HoomdossierSession::getBuilding()) {
            return true;
        }

        return false;
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
}
