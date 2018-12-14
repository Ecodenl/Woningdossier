<?php

namespace App\Providers;

use App\Models\Building;
use App\Models\BuildingPermission;
use App\Models\Questionnaire;
use App\Models\Step;
use App\Models\User;
use App\Policies\QuestionnairePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Questionnaire::class => QuestionnairePolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }
}
