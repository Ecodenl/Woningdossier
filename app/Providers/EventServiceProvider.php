<?php

namespace App\Providers;

use App\Events\FillingToolForUserEvent;
use App\Events\ObservingToolForUserEvent;
use App\Events\ParticipantAddedEvent;
use App\Events\ParticipantRevokedEvent;
use App\Events\PrivateMessageReceiverEvent;
use App\Events\StepDataHasBeenChangedEvent;
use App\Events\UserChangedHisEmailEvent;
use App\Listeners\FillingToolForUserListener;
use App\Listeners\LogRegisteredUserListener;
use App\Listeners\ObservingToolForUserListener;
use App\Listeners\ParticipantAddedListener;
use App\Listeners\ParticipantRevokedListener;
use App\Listeners\PrivateMessageReceiverListener;
use App\Listeners\SendUserChangeEmailConfirmationListener;
use App\Listeners\StepDataHasBeenChangedListener;
use App\Listeners\SuccessFullLoginListener;
use App\Listeners\UserEventSubscriber;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        PrivateMessageReceiverEvent::class => [
            PrivateMessageReceiverListener::class,
        ],
        ParticipantRevokedEvent::class => [
            ParticipantRevokedListener::class,
        ],
        ParticipantAddedEvent::class => [
            ParticipantAddedListener::class,
        ],
        Login::class => [
            SuccessFullLoginListener::class,
        ],
        Registered::class => [
            LogRegisteredUserListener::class
        ],
        FillingToolForUserEvent::class => [
            FillingToolForUserListener::class
        ],
        ObservingToolForUserEvent::class => [
            ObservingToolForUserListener::class
        ],
        StepDataHasBeenChangedEvent::class => [
            StepDataHasBeenChangedListener::class
        ],
        UserChangedHisEmailEvent::class => [
            SendUserChangeEmailConfirmationListener::class
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
