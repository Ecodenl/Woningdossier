<?php

namespace App\Providers;

use App\Models\Account;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\FileStorage;
use App\Models\PrivateMessage;
use App\Models\Questionnaire;
use App\Models\User;
use App\NotificationSetting;
use App\Policies\BuildingPolicy;
use App\Policies\CooperationPolicy;
use App\Policies\FileStoragePolicy;
use App\Policies\NotificationSettingPolicy;
use App\Policies\PrivateMessagePolicy;
use App\Policies\QuestionnairePolicy;
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
        PrivateMessage::class => PrivateMessagePolicy::class,
        Questionnaire::class => QuestionnairePolicy::class,
        Cooperation::class => CooperationPolicy::class,
        User::class => UserPolicy::class,
        Building::class => BuildingPolicy::class,
        NotificationSetting::class => NotificationSettingPolicy::class,
        FileStorage::class => FileStoragePolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('access-admin', 'App\Policies\UserPolicy@accessAdmin');
        Gate::define('delete-user', 'App\Policies\UserPolicy@deleteUser');
        Gate::define('participate-in-group-chat', 'App\Policies\UserPolicy@participateInGroupChat');
        Gate::define('remove-participant-from-chat', 'App\Policies\UserPolicy@removeParticipantFromChat');

        Gate::define('access-building', BuildingPolicy::class.'@accessBuilding');
        Gate::define('delete-own-account', UserPolicy::class.'@deleteOwnAccount');
        Gate::define('talk-to-resident', UserPolicy::class.'@talkToResident');
        Gate::define('assign-role', UserPolicy::class.'@assignRole');
        Gate::define('assign-role-to-user', UserPolicy::class.'@assignRoleToUser');

    }

    public function register()
    {

        // custom user resolver via account
        \Auth::resolveUsersUsing(function($guard = null) {
            return \Auth::guard($guard)->user() instanceof Account ? \Auth::guard()->user()->user() : null;
        });
    }
}
