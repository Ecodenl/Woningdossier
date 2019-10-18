<?php

namespace App\Policies;

use App\Helpers\HoomdossierSession;
use App\Models\Cooperation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CooperationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the cooperation.
     *
     * @param \App\Models\User        $user
     * @param \App\Models\Cooperation $cooperation
     *
     * @return bool
     */
    public function edit(User $user, Cooperation $cooperation): bool
    {
        return 'super-admin' == (bool) HoomdossierSession::currentRole();
    }

    /**
     * Determine whether the user can create cooperations.
     *
     * @param \App\Models\User $user
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return 'super-admin' == (bool) HoomdossierSession::currentRole();
    }

    /**
     * Determine whether the user can update the cooperation.
     *
     * @param \App\Models\User        $user
     * @param \App\Models\Cooperation $cooperation
     *
     * @return mixed
     */
    public function update(User $user, Cooperation $cooperation)
    {
        return 'super-admin' == (bool) HoomdossierSession::currentRole();
    }

    /**
     * Determine whether the user can delete the cooperation.
     *
     * @param \App\Models\User        $user
     * @param \App\Models\Cooperation $cooperation
     *
     * @return mixed
     */
    public function delete(User $user, Cooperation $cooperation)
    {
        return 'super-admin' == (bool) HoomdossierSession::currentRole();
    }
}
