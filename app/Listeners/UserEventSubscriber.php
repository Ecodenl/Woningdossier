<?php

namespace App\Listeners;

use App\Events\Registered;
use App\Jobs\SendRequestAccountConfirmationEmail;

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
        SendRequestAccountConfirmationEmail::dispatch($event->user, $event->cooperation);
    }
}
