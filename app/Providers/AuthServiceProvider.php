<?php

namespace App\Providers;

use App\Models\Building;
use App\Models\PrivateMessage;
use App\Models\Questionnaire;
use App\Models\User;
use App\Policies\PrivateMessagePolicy;
use App\Policies\QuestionnairePolicy;
use App\Policies\UserPolicy;
use App\Services\HoomSessionGuard;
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
        User::class => UserPolicy::class,
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
        Gate::define('respond', 'App\Policies\UserPolicy@respond');
        Gate::define('make-appointment', 'App\Policies\UserPolicy@makeAppointment');
        Gate::define('participate-in-group-chat', 'App\Policies\UserPolicy@participateInGroupChat');
        Gate::define('remove-participant-from-chat', 'App\Policies\UserPolicy@removeParticipantFromChat');
        Gate::define('access-building', 'App\Policies\UserPolicy@accessBuilding');

    }
}
