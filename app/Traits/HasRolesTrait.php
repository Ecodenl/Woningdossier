<?php

namespace App\Traits;

use App\Helpers\HoomdossierSession;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;
use Spatie\Permission\Contracts\Role;
use Spatie\Permission\Traits\HasRoles;

trait HasRolesTrait {

    use HasRoles;

    /**
     * @param  null  $cooperationId
     *
     * @return MorphToMany
     */
    public function roles($cooperationId = null): MorphToMany
    {
        // if the cooperationId is not given we will try to obtain it from the session.
        if (is_null($cooperationId) && HoomdossierSession::hasCooperation()) {
            $cooperationId = HoomdossierSession::getCooperation();
        }

        return $this->morphToMany(
            config('permission.models.role'),
            'model',
            config('permission.table_names.model_has_roles'),
            config('permission.column_names.model_morph_key'),
            'role_id'
        )->wherePivot('cooperation_id', $cooperationId);
    }

    /**
     * Determine if the model has (one of) the given role(s).
     *
     * @param null $cooperationId
     * @param string|int|array|\Spatie\Permission\Contracts\Role|\Illuminate\Support\Collection $roles
     *
     * @return bool
     */
    public function hasRole($roles, $cooperationId = null): bool
    {
        $userRoles = $this->roles($cooperationId)->get();

        if (is_string($roles) && false !== strpos($roles, '|')) {
            $roles = $this->convertPipeToArray($roles);
        }

        if (is_string($roles)) {
            return $userRoles->contains('name', $roles);
        }

        if (is_int($roles)) {
            return $userRoles->contains('id', $roles);
        }

        if ($roles instanceof Role) {
            return $userRoles->contains('id', $roles->id);
        }

        if (is_array($roles)) {
            foreach ($roles as $role) {
                if ($this->hasRole($role, $cooperationId)) {
                    return true;
                }
            }

            return false;
        }

        return $roles->intersect($userRoles)->isNotEmpty();
    }

    /**
     * Assign the given role to the model.
     *
     * @param array|string|\Spatie\Permission\Contracts\Role ...$roles
     *
     * @return $this
     */
    public function assignRole($cooperationId = null, ...$roles)
    {
        $roles = collect($roles)
            ->flatten()
            ->map(function ($role) {
                if (empty($role)) {
                    return false;
                }
                return $this->getStoredRole($role);
            })
            ->filter(function ($role) {
                return $role instanceof Role;
            })
            ->each(function ($role) {
                $this->ensureModelSharesGuard($role);
            })
            ->map->id
            ->all();

        $rolesToSync = [];

        foreach ($roles as $roleId) {
            $rolesToSync[$roleId] = ['cooperation_id' => $cooperationId];
        }
//
//        $rolesToSync = $roles;

        $model = $this->getModel();

        if ($model->exists) {
            $this->roles($cooperationId)->sync($rolesToSync, false);
            $model->load('roles');
        } else {
            $class = \get_class($model);

            $class::saved(
                function ($object) use ($cooperationId, $rolesToSync, $model) {
                    static $modelLastFiredOn;
                    if ($modelLastFiredOn !== null && $modelLastFiredOn === $model) {
                        return;
                    }
                    $object->roles()->sync($rolesToSync, false);
                    $object->load('roles');
                    $modelLastFiredOn = $object;
                });
        }

        $this->forgetCachedPermissions();

        return $this;
    }

    /**
     * @param  null  $cooperationId
     *
     * @return Collection
     */
    public function getRoleNames($cooperationId = null): Collection
    {
        return $this->roles($cooperationId)->pluck('name');
    }
}