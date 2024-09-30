<?php

namespace App\Providers;

use App\Policies\BuildingPolicy;
use App\Policies\RolePolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // Use auto discovery
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::guessPolicyNamesUsing(function ($modelClass) {
            return 'App\\Policies\\'.class_basename($modelClass).'Policy';
        });

        Gate::define('talk-to-resident', BuildingPolicy::class.'@talkToResident');
        Gate::define('access-building', BuildingPolicy::class.'@accessBuilding');
        Gate::define('set-appointment', BuildingPolicy::class.'@setAppointment');
        Gate::define('set-status', BuildingPolicy::class.'@setStatus');

        Gate::define('delete-own-account', UserPolicy::class.'@deleteOwnAccount');
        Gate::define('assign-role', UserPolicy::class.'@assignRole');
        Gate::define('access-admin', UserPolicy::class.'@accessAdmin');
        Gate::define('delete-user', UserPolicy::class.'@deleteUser');
        Gate::define('remove-participant-from-chat', UserPolicy::class.'@removeParticipantFromChat');

        Gate::define('send-user-information-to-econobis', [UserPolicy::class, 'sendUserInformationToEconobis']);
        Gate::define('editAny', [RolePolicy::class, 'editAny']);
    }
}
