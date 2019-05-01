<?php

namespace App\Policies;

use App\Helpers\HoomdossierSession;
use App\Models\User;
use App\Models\Cooperation;
use Illuminate\Auth\Access\HandlesAuthorization;

class CooperationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the cooperation.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Cooperation  $cooperation
     * @return bool
     */
    public function edit(User $user, Cooperation $cooperation): bool
    {
        return (bool) HoomdossierSession::currentRole() == 'super-admin';
    }

    /**
     * Determine whether the user can create cooperations.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return (bool) HoomdossierSession::currentRole() == 'super-admin';
    }

    /**
     * Determine whether the user can update the cooperation.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Cooperation  $cooperation
     * @return mixed
     */
    public function update(User $user, Cooperation $cooperation)
    {
        return (bool) HoomdossierSession::currentRole() == 'super-admin';
    }

    /**
     * Determine whether the user can delete the cooperation.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Cooperation  $cooperation
     * @return mixed
     */
    public function delete(User $user, Cooperation $cooperation)
    {
        return (bool) HoomdossierSession::currentRole() == 'super-admin';
    }
}
