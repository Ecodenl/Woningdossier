<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * App\Models\Cooperation.
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $website_url
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cooperation whereWebsiteUrl($value)
 * @mixin \Eloquent
 */
class Cooperation extends Model
{
    public $fillable = [
        'name', 'website_url', 'slug', 'cooperation_email',
    ];

    /**
     * The users associated with this cooperation.
     */
    public function users()
    {
        return $this->hasMany(User::class);
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
     * Get the sub steps for a given step.
     *
     * @param Step $step
     * @return mixed
     */
    public function getSubStepsForStep(Step $step)
    {
        return $this->steps()->subStepsForStep($step)->activeOrderedSteps()->get();
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
     * get the active steps with its substeps ordered on the order column.
     *
     * @deprecated should use the steps() relation and activeOrderedSteps scope.
     * @return mixed
     */
    public function getActiveOrderedSteps()
    {
//        return $this->steps()
            // for now, should be removed when the step is deleted
//            ->where('steps.short', '!=', 'building-detail')
//            ->where('steps.parent_id', '=', null)
//            ->orderBy('cooperation_steps.order')
//            ->where('cooperation_steps.is_active', '1')
//            ->get();
        return \App\Helpers\Cache\Cooperation::getActiveOrderedSteps($this);
        //return $this->steps()->orderBy('cooperation_steps.order')->where('cooperation_steps.is_active', '1')->get();
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

        return $coaches;
    }

    /**
     * Return a collection of users for the cooperation and given role.
     *
     * This does not apply any scopes and should probably only be used in admin environments.
     *
     * @param Role $role
     *
     * @return Collection
     */
    public function getUsersWithRole(Role $role): Collection
    {
        return User::hydrate(
            \DB::table(config('permission.table_names.model_has_roles'))
                ->where('cooperation_id', $this->id)
                ->where('role_id', $role->id)
                ->leftJoin('users', config('permission.table_names.model_has_roles') . '.' . config('permission.column_names.model_morph_key'), '=', 'users.id')
                ->get()->toArray()
        );
    }

//    public function scopeUsersWithRole(Builder $query, Role $role)
//    {
//        return $query
//            ->leftJoin(config('permission.table_names.model_has_roles'), 'cooperations.id', '=', 'model_has_roles.cooperation_id')
//            ->where('model_has_roles.role_id', $role->id)
//            ->leftJoin('users', config('permission.table_names.model_has_roles').'.'.config('permission.column_names.model_morph_key'), '=', 'users.id')
//            ->select('users.*');
//    }
//
}
