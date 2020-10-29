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
     */
    public function edit(User $user, Cooperation $cooperation): bool
    {
        return 'super-admin' == (bool) HoomdossierSession::currentRole();
    }

    /**
     * Determine whether the user can create cooperations.
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
     * @return mixed
     */
    public function update(User $user, Cooperation $cooperation)
    {
        return 'super-admin' == (bool) HoomdossierSession::currentRole();
    }

    /**
     * Determine whether the user can delete the cooperation.
     *
     * @return mixed
     */
    public function delete(User $user, Cooperation $cooperation)
    {
        // hoom mag niet.
        if ('hoom' !== $cooperation->slug) {
            return 'super-admin' == (bool) HoomdossierSession::currentRole();
        }

        return false;
    }
}
