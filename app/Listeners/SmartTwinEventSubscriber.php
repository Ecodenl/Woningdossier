<?php

namespace App\Listeners;

use App\Events\AccountVerified;
use App\Events\SmartTwinCallbackReceived;
use App\Helpers\RoleHelper;
use App\Jobs\SmartTwin\Out\CreateCoachAccount;
use App\Jobs\SmartTwin\Out\CreateUserAccount;
use App\Jobs\SmartTwin\Out\GetAdviceResults;
use App\Models\Account;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Events\RoleAttached;

class SmartTwinEventSubscriber
{
    private const string DISPATCH_LOCK_PREFIX = 'smarttwin-create-account-';

    // Long enough for a worker to pick the job up and write the id back, short enough that a failed
    // attempt is retried on the next login rather than being blocked for the rest of the day.
    private const int DISPATCH_LOCK_SECONDS = 300;

    /*public function subscribe(Dispatcher $events): array
    {
        return [
            AccountVerified::class => 'handleAccountVerified',
            RoleAttached::class => 'handleRoleAttached',
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

        // The link lives on the account, since SmartTwin keys its users on the e-mail address.
        if (! empty($event->user->smartTwinUserId())) {
            return;
        }

        $user = $event->user->user();

        if (! $user instanceof User) {
            return;
        }

        // If the user for some odd reason has no role attached, attach the resident rol to him.
        if ($user->roles()->doesntExist()) {
            $user->assignRole(Role::findByName(RoleHelper::ROLE_RESIDENT));
        }

        $this->dispatchForAccount($event->user);
    }

    public function handleAccountVerified(AccountVerified $event): void
    {
        $this->dispatchForAccount($event->account);
    }

    // RoleAttached fires on every assignRole() / syncRoles(). When the account is not yet verified,
    // we skip — handleAccountVerified() will pick it up once verification happens. The attached role
    // itself is not used: dispatchForAccount() re-reads all roles and decides from the full set.
    public function handleRoleAttached(RoleAttached $event): void
    {
        if (! $event->model instanceof User) {
            return;
        }

        $account = $event->model->account;
        if (! $account?->hasVerifiedEmail()) {
            return;
        }

        $this->dispatchForAccount($account);
    }

    public function handleSmartTwinCallbackReceived(SmartTwinCallbackReceived $event): void
    {
        foreach ($event->addedCallbacks as $callbackData) {
            GetAdviceResults::dispatch($callbackData, $event->building->getKey());
        }
    }

    // An account gets exactly one SmartTwin user (they key on e-mail address), so one role has to win
    // across every cooperation this account is a member of. Coach beats resident: coaching is the
    // professional function and the advisor tool is unreachable without an Advisor account. The
    // trade-off is that a coach cannot open the quickscan for their own home — SmartTwin refuses a
    // quick-scan/link for an Advisor account. Roles that have no SmartTwin equivalent (coordinator,
    // cooperation-admin, ...) are skipped, so such an account gets no SmartTwin user at all.
    //
    // Reading roles fresh across all users keeps this deterministic: it no longer depends on which
    // cooperation happened to trigger creation first, nor on the order jobs come off the queue.
    private function dispatchForAccount(Account $account): void
    {
        if (! empty($account->smartTwinUserId())) {
            return;
        }

        $users = $account->users()->forAllCooperations()->get();

        $coach    = $users->first(fn (User $user) => $user->hasRole(RoleHelper::ROLE_COACH));
        $resident = $coach instanceof User
            ? null
            : $users->first(fn (User $user) => $user->hasRole(RoleHelper::ROLE_RESIDENT));

        if (! $coach instanceof User && ! $resident instanceof User) {
            return;
        }

        // The guard at the top only sees work that has finished. A single login can reach this twice —
        // our own resident fallback fires RoleAttached, which lands back here before the queued job has
        // written anything — and two workers can pick up the same account at once. Either way the second
        // create is a duplicate that SmartTwin rejects with a 400. Cache::add is atomic, so only the
        // first caller gets through, and the lock is short-lived because every login retries anyway.
        if (! Cache::add(self::DISPATCH_LOCK_PREFIX . $account->id, true, self::DISPATCH_LOCK_SECONDS)) {
            return;
        }

        $coach instanceof User
            ? CreateCoachAccount::dispatch($coach)
            : CreateUserAccount::dispatch($resident);
    }
}
