<?php

namespace App\Listeners;

use App\Events\AccountVerified;
use App\Events\SmartTwinCallbackReceived;
use App\Events\UserDeleted;
use App\Helpers\RoleHelper;
use App\Jobs\SmartTwin\Out\CreateCoachAccount;
use App\Jobs\SmartTwin\Out\CreateUserAccount;
use App\Jobs\SmartTwin\Out\DeleteAccount;
use App\Jobs\SmartTwin\Out\GetAdviceResults;
use App\Models\Account;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Login;
use Spatie\Permission\Events\RoleAttached;

class SmartTwinEventSubscriber
{

    /*public function subscribe(Dispatcher $events): array
    {
        return [
            AccountVerified::class => 'handleAccountVerified',
            RoleAttached::class => 'handleRoleAttached',
            UserDeleted::class => 'handleUserDeleted',
            SmartTwinCallbackReceived::class => 'handleSmartTwinCallbackReceived',
            Login::class => 'handleLogin',
        ];
    }*/

    // Fires on authentication, before the user picks a role on the choose-roles screen, so there is
    // no role in the session yet to go on. Only a backfill: users verified after this feature shipped
    // already got their account via handleAccountVerified().
    public function handleLogin(Login $event): void
    {
        if (! $event->user instanceof Account) {
            return;
        }

        $user = $event->user->user();

        if (! $user instanceof User) {
            return;
        }

        // check the extra: is there already a smart_twin_user_id
        if (! empty($user->extra['smarttwin_user_id'] ?? null)) {
            return;
        }

        // If the user for some odd reason has no role attached, attach the resident rol to him.
        if ($user->roles()->doesntExist()) {
            $user->assignRole(Role::findByName(RoleHelper::ROLE_RESIDENT));
        }

        $this->dispatchForUser($user);
    }

    public function handleAccountVerified(AccountVerified $event): void
    {
        foreach ($event->account->users as $user) {
            $this->dispatchForUser($user);
        }
    }

    // RoleAttached fires on every assignRole() / syncRoles(). When the account is not yet verified,
    // we skip — handleAccountVerified() will pick it up once verification happens. The attached role
    // itself is not used: dispatchForUser() re-reads all roles and decides from the full set.
    public function handleRoleAttached(RoleAttached $event): void
    {
        if (! $event->model instanceof User) {
            return;
        }

        $user = $event->model;
        if (! $user->account?->hasVerifiedEmail()) {
            return;
        }

        $this->dispatchForUser($user);
    }

    public function handleUserDeleted(UserDeleted $event): void
    {
        $guid = $event->context['extra']['smarttwin_user_id'] ?? null;
        if (! empty($guid)) {
            DeleteAccount::dispatch($guid);
        }
    }

    public function handleSmartTwinCallbackReceived(SmartTwinCallbackReceived $event): void
    {
        foreach ($event->addedCallbacks as $callbackData) {
            GetAdviceResults::dispatch($callbackData, $event->building->getKey());
        }
    }

    // A user gets at most one SmartTwin account, so one role has to win. Coach beats resident, so a
    // user holding both is created as UserRole::Advisor. Anything else (coordinator, cooperation-admin,
    // ...) has no SmartTwin equivalent and is skipped — such a user gets no account at all.
    // Roles are read fresh so a role attached moments ago is never missed by a stale relation.
    private function dispatchForUser(User $user): void
    {
        $roleNames = $user->roles()->pluck('name');

        if ($roleNames->contains(RoleHelper::ROLE_COACH)) {
            CreateCoachAccount::dispatch($user);

            return;
        }

        if ($roleNames->contains(RoleHelper::ROLE_RESIDENT)) {
            CreateUserAccount::dispatch($user);
        }
    }
}
