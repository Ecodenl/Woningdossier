<?php

namespace App\Listeners;

use App\Helpers\HoomdossierSession;
use App\Jobs\SendRequestAccountConfirmationEmail;
use App\Models\Cooperation;
use Illuminate\Auth\Events\Registered;

class UserEventSubscriber
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    public function subscribe($events)
    {
        $events->listen(
            Registered::class,
            self::class.'@onUserRegistration'
        );
    }

    /**
     * Handle the event.
     *
     * @param Registered $event
     *
     * @return void
     */
    public function onUserRegistration(Registered $event)
    {
        $cooperation = Cooperation::find(HoomdossierSession::getCooperation());
        SendRequestAccountConfirmationEmail::dispatch($event->user, $cooperation);
    }
}
