<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * App\Models\Cooperation.
 *
 * @property int $id
 * @property string $name
 * @property string $website_url
 * @property string $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Step[] $steps
 * @property \App\Models\CooperationStyle $style
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cooperation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cooperation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cooperation query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cooperation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cooperation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cooperation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cooperation whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cooperation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Cooperation extends Model
{
    public $fillable = [
        'name',
        'website_url',
        'slug',
    ];

    /**
     * The users associated with this cooperation.
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function style()
    {
        return $this->hasOne(CooperationStyle::class);
    }

    /**
     * Get all the steps from the cooperation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function steps()
    {
        return $this->belongsToMany(Step::class, 'cooperation_steps')->withPivot('order', 'is_active');
    }

    /**
     * Check if the cooperation has a active step.
     *
     * @param Step $step
     *
     * @return bool
     */
    public function isStepActive(Step $step): bool
    {
        $cooperationSteps = $this->steps();
        $cooperationStep = $cooperationSteps->find($step->id);
        if ($cooperationStep instanceof Step) {
            if ($cooperationStep->pivot->is_active) {
                return true;
            }
        }

        return false;
    }

    /**
     * get the active steps ordered on the order column.
     *
     * @return mixed
     */
    public function getActiveOrderedSteps(): Collection
    {
        return $this->steps()
            ->orderBy('cooperation_steps.order')
            ->where('cooperation_steps.is_active', '1')->get();
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Return the coaches from the current cooperation.
     *
     * @return $this
     */
    public function getCoaches()
    {
        $coaches = $this->users()->role('coach');

//        $query = \DB::table('cooperations')
//            ->select('users.*')
//            ->where('cooperations.id', '=', $this->id)
//            ->join('cooperation_user', 'cooperations.id', '=', 'cooperation_user.cooperation_id')
//            ->join('model_has_roles', 'cooperation_user.user_id', '=', 'model_has_roles.model_id')
//            ->where('model_has_roles.role_id', '=', 4)
//            ->join('users', 'cooperation_user.user_id', '=', 'users.id');

        return $coaches;
    }

    /**
     * Return the residents from the current cooperation.
     *
     * @return $this
     */
    public function getResidents()
    {
        $users = $this->users()->role('resident');

        return $users;

//        return $query = \DB::table('cooperations')
//        ->select('users.*')
//        ->where('cooperations.id', '=', $this->id)
//        ->leftJoin('cooperation_user', 'cooperations.id', '=', 'cooperation_user.cooperation_id')
//        ->leftJoin('model_has_roles', 'cooperation_user.user_id', '=', 'model_has_roles.model_id')
//        ->where('model_has_roles.role_id', '=', 5)
//        ->leftJoin('users', 'cooperation_user.user_id', '=', 'users.id');
    }

    public function getCoordinators()
    {
        $users = $this->users()->role('coordinator');

        return $users;
    }
}
