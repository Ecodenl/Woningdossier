<?php

namespace App\Providers;

use App\Models\Building;
use App\Models\PrivateMessage;
use App\Models\Questionnaire;
use App\Policies\PrivateMessagePolicy;
use App\Policies\QuestionnairePolicy;
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
        Gate::define('respond', 'App\Policies\UserPolicy@respond');
        Gate::define('make-appointment', 'App\Policies\UserPolicy@makeAppointment');
        Gate::define('access-building', 'App\Policies\UserPolicy@accessBuilding');
    }
}
