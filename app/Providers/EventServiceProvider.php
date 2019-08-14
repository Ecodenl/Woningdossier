<?php

namespace App\Providers;

use App\Events\DossierResetPerformed;
use App\Events\FillingToolForUserEvent;
use App\Events\ObservingToolForUserEvent;
use App\Events\ParticipantAddedEvent;
use App\Events\ParticipantRevokedEvent;
use App\Events\PrivateMessageReceiverEvent;
use App\Events\StepDataHasBeenChanged;
use App\Events\UserAllowedAccessToHisBuilding;
use App\Events\UserAssociatedWithOtherCooperation;
use App\Events\UserChangedHisEmailEvent;
use App\Events\UserRevokedAccessToHisBuilding;
use App\Listeners\DossierResetListener;
use App\Listeners\FillingToolForUserListener;
use App\Listeners\LogAllowedAccessToBuilding;
use App\Listeners\LogRegisteredUserListener;
use App\Listeners\LogRevokedAccessToBuilding;
use App\Listeners\LogUserAssociatedWithOtherCooperation;
use App\Listeners\ObservingToolForUserListener;
use App\Listeners\ParticipantAddedListener;
use App\Listeners\ParticipantRevokedListener;
use App\Listeners\PrivateMessageReceiverListener;
use App\Listeners\SetOldEmailListener;
use App\Listeners\StepDataHasBeenChangedListener;
use App\Listeners\SuccessFullLoginListener;
use App\Listeners\UserEventSubscriber;
use Illuminate\Auth\Events\Login;
use App\Events\Registered;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        DossierResetPerformed::class => [
            DossierResetListener::class,
        ],
        PrivateMessageReceiverEvent::class => [
            PrivateMessageReceiverListener::class,
        ],
        ParticipantRevokedEvent::class => [
            ParticipantRevokedListener::class,
        ],
        ParticipantAddedEvent::class => [
            ParticipantAddedListener::class,
        ],
        Login::class                              => [
            SuccessFullLoginListener::class,
        ],
        Registered::class                         => [
            LogRegisteredUserListener::class
        ],
        UserAssociatedWithOtherCooperation::class => [
            LogUserAssociatedWithOtherCooperation::class
        ],
        FillingToolForUserEvent::class            => [
            FillingToolForUserListener::class
        ],
        ObservingToolForUserEvent::class          => [
            ObservingToolForUserListener::class
        ],
        StepDataHasBeenChanged::class             => [
            StepDataHasBeenChangedListener::class
        ],
        UserChangedHisEmailEvent::class           => [
            SetOldEmailListener::class,
        ],
        UserAllowedAccessToHisBuilding::class     => [
            LogAllowedAccessToBuilding::class
        ],
        UserRevokedAccessToHisBuilding::class     => [
            LogRevokedAccessToBuilding::class
        ],
    ];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        UserEventSubscriber::class,
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
