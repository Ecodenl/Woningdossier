<?php

namespace App\Listeners;

use App\Events\AccountVerified;
use App\Helpers\RoleHelper;
use App\Jobs\SmartTwin\Out\CreateCoachAccount;
use App\Jobs\SmartTwin\Out\CreateUserAccount;
use App\Models\Role;
use App\Models\User;
use Illuminate\Events\Dispatcher;
use Spatie\Permission\Events\RoleAttached;

class SmartTwinEventSubscriber
{
    public function handleAccountVerified(AccountVerified $event): void
    {
        foreach ($event->account->users as $user) {
            foreach ($user->roles as $role) {
                $this->dispatchForRole($user, $role->name);
            }
        }
    }

    // RoleAttached fires on every assignRole() / syncRoles(). When the account is not yet verified,
    // we skip — handleAccountVerified() will pick it up once verification happens. This relies on
    // CreatesUsers::createUser running UserService::register (which fires RoleAttached) BEFORE
    // markEmailAsVerified() (which fires AccountVerified). If that order ever flips, dispatches may double.
    public function handleRoleAttached(RoleAttached $event): void
    {
        if (! $event->model instanceof User) {
            return;
        }

        $user = $event->model;
        if (! $user->account?->hasVerifiedEmail()) {
            return;
        }

        foreach ($this->resolveAttachedRoleNames($event) as $roleName) {
            $this->dispatchForRole($user, $roleName);
        }
    }

    public function subscribe(Dispatcher $events): array
    {
        return [
            AccountVerified::class => 'handleAccountVerified',
            RoleAttached::class => 'handleRoleAttached',
        ];
    }

    private function dispatchForRole(User $user, string $roleName): void
    {
        match ($roleName) {
            RoleHelper::ROLE_RESIDENT => CreateUserAccount::dispatch($user),
            RoleHelper::ROLE_COACH => CreateCoachAccount::dispatch($user),
            default => null,
        };
    }

    // Spatie's RoleAttached carries roles as `$rolesOrIds` — can be names, IDs, or Role instances.
    // Normalize to a flat list of role names so the dispatcher only needs to match on name.
    private function resolveAttachedRoleNames(RoleAttached $event): array
    {
        $items = collect($event->rolesOrIds ?? [])->flatten();

        $names = $items->filter(fn ($r) => is_object($r) || ! ctype_digit((string) $r))
            ->map(fn ($r) => is_object($r) ? $r->name : (string) $r);

        $ids = $items->filter(fn ($r) => ! is_object($r) && ctype_digit((string) $r));

        if ($ids->isNotEmpty()) {
            $names = $names->merge(
                Role::whereIn('id', $ids->all())->pluck('name')
            );
        }

        return $names->unique()->values()->all();
    }
}
