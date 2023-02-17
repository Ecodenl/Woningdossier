<?php

namespace App\Providers;

use App\Events\BuildingAddressUpdated;
use App\Events\CooperationMeasureApplicationUpdated;
use App\Events\CustomMeasureApplicationChanged;
use App\Events\FillingToolForUserEvent;
use App\Events\NoMappingFoundForBagMunicipality;
use App\Events\ObservingToolForUserEvent;
use App\Events\ParticipantAddedEvent;
use App\Events\ParticipantRevokedEvent;
use App\Events\PrivateMessageReceiverEvent;
use App\Events\Registered;
use App\Events\StepCleared;
use App\Events\StepDataHasBeenChanged;
use App\Events\UserAllowedAccessToHisBuilding;
use App\Events\UserAssociatedWithOtherCooperation;
use App\Events\UserChangedHisEmailEvent;
use App\Events\UserRevokedAccessToHisBuilding;
use App\Listeners\AuditedListener;
use App\Listeners\CreateTargetlessMappingForMunicipality;
use App\Listeners\DeleteUserActionPlanAdvicesForStep;
use App\Listeners\FillingToolForUserListener;
use App\Listeners\GiveCoachesBuildingPermission;
use App\Listeners\LogAllowedAccessToBuilding;
use App\Listeners\LogFillingToolForUserListener;
use App\Listeners\LogObservingToolForUserListener;
use App\Listeners\LogRegisteredUserListener;
use App\Listeners\LogRevokedAccessToBuilding;
use App\Listeners\LogUserAssociatedWithOtherCooperation;
use App\Listeners\ObservingToolForUserListener;
use App\Listeners\ParticipantAddedListener;
use App\Listeners\ParticipantRevokedListener;
use App\Listeners\PrivateMessageReceiverListener;
use App\Listeners\RefreshRelatedAdvices;
use App\Listeners\RefreshUserHisAdvices;
use App\Listeners\RevokeBuildingPermissionForCoaches;
use App\Listeners\SendUserAssociatedWithCooperationMail;
use App\Listeners\SetMessagesReadForBuilding;
use App\Listeners\SetMessagesUnreadForRevokedUserOnBuilding;
use App\Listeners\SetOldEmailListener;
use App\Listeners\StepDataHasBeenChangedListener;
use App\Listeners\SuccessFullLoginListener;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use OwenIt\Auditing\Events\Audited;
use Sentry\State\Scope;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        NoMappingFoundForBagMunicipality::class => [
            CreateTargetlessMappingForMunicipality::class
        ],
        PrivateMessageReceiverEvent::class => [
            PrivateMessageReceiverListener::class,
        ],
        CooperationMeasureApplicationUpdated::class => [
            RefreshRelatedAdvices::class,
        ],
        CustomMeasureApplicationChanged::class => [
            RefreshRelatedAdvices::class
        ],
        BuildingAddressUpdated::class => [
            RefreshUserHisAdvices::class
        ],
        Login::class => [
            SuccessFullLoginListener::class,
        ],
        Registered::class => [
            LogRegisteredUserListener::class,
        ],
        UserAssociatedWithOtherCooperation::class => [
            LogUserAssociatedWithOtherCooperation::class,
            SendUserAssociatedWithCooperationMail::class,
        ],
        FillingToolForUserEvent::class => [
            FillingToolForUserListener::class,
            LogFillingToolForUserListener::class,
        ],
        ObservingToolForUserEvent::class => [
            ObservingToolForUserListener::class,
            LogObservingToolForUserListener::class,
        ],
        StepDataHasBeenChanged::class => [
            StepDataHasBeenChangedListener::class,
        ],
        UserChangedHisEmailEvent::class => [
            SetOldEmailListener::class,
        ],
        ParticipantRevokedEvent::class => [
            ParticipantRevokedListener::class,
            SetMessagesUnreadForRevokedUserOnBuilding::class,
        ],
        ParticipantAddedEvent::class => [
            ParticipantAddedListener::class,
        ],
        UserAllowedAccessToHisBuilding::class => [
            GiveCoachesBuildingPermission::class,
            LogAllowedAccessToBuilding::class,
        ],
        UserRevokedAccessToHisBuilding::class => [
            LogRevokedAccessToBuilding::class,
            RevokeBuildingPermissionForCoaches::class,
            SetMessagesReadForBuilding::class,
        ],
        StepCleared::class => [
            DeleteUserActionPlanAdvicesForStep::class,
        ],
        Audited::class => [
            AuditedListener::class,
        ],
    ];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        // subscribers
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        \Sentry\configureScope(function (Scope $scope) {
            $scope->setTag('APP_URL', config("app.url"));
        });
    }
}
