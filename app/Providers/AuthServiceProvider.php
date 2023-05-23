<?php

namespace App\Providers;

use App\Models\Building;
use App\Models\Cooperation;
use App\Models\FileStorage;
use App\Models\Media;
use App\Models\PrivateMessage;
use App\Models\Questionnaire;
use App\Models\Role;
use App\Models\SubStep;
use App\Models\ToolQuestion;
use App\Models\User;
use App\Models\UserActionPlanAdvice;
use App\Policies\BuildingPolicy;
use App\Policies\CooperationPolicy;
use App\Policies\FileStoragePolicy;
use App\Policies\MediaPolicy;
use App\Policies\PrivateMessagePolicy;
use App\Policies\QuestionnairePolicy;
use App\Policies\RolePolicy;
use App\Policies\SubStepPolicy;
use App\Policies\ToolQuestionPolicy;
use App\Policies\UserActionPlanAdvicePolicy;
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
        SubStep::class => SubStepPolicy::class,
        ToolQuestion::class => ToolQuestionPolicy::class,
        PrivateMessage::class => PrivateMessagePolicy::class,
        Questionnaire::class => QuestionnairePolicy::class,
        Cooperation::class => CooperationPolicy::class,
        User::class => UserPolicy::class,
        Building::class => BuildingPolicy::class,
        FileStorage::class => FileStoragePolicy::class,
        UserActionPlanAdvice::class => UserActionPlanAdvicePolicy::class,
        Media::class => MediaPolicy::class,
        Role::class => RolePolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

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
