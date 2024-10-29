<?php

namespace App\Policies;

use App\Helpers\RoleHelper;
use App\Models\CooperationMeasureApplication;
use App\Models\Account;
use Illuminate\Auth\Access\HandlesAuthorization;

class CooperationMeasureApplicationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(Account $account): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(Account $account, CooperationMeasureApplication $cooperationMeasureApplication): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(Account $account): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(Account $account, CooperationMeasureApplication $cooperationMeasureApplication): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(Account $account, CooperationMeasureApplication $cooperationMeasureApplication): bool
    {
        return $account->user()->hasRoleAndIsCurrentRole(RoleHelper::ROLE_COOPERATION_ADMIN) && $cooperationMeasureApplication->is_deletable;
    }

    /**
     * Determine whether the user can restore the model.
     *
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(Account $account, CooperationMeasureApplication $cooperationMeasureApplication): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(Account $account, CooperationMeasureApplication $cooperationMeasureApplication): bool
    {
        //
    }
}
