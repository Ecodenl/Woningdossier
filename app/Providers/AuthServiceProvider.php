<?php

namespace App\Providers;

use App\Models\Building;
use App\Models\Cooperation;
use App\Models\PrivateMessage;
use App\Models\Questionnaire;
use App\Models\User;
use App\Policies\BuildingPolicy;
use App\Policies\CooperationPolicy;
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
        Building::class => BuildingPolicy::class
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

        Gate::define('view-building-info', BuildingPolicy::class.'@viewBuildingInfo');

        Gate::define('access-building', BuildingPolicy::class.'@accessBuilding');

        Gate::define('delete-own-account', UserPolicy::class.'@deleteOwnAccount');
    }
}
