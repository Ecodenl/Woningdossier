<?php

namespace App\Listeners;

use App\Mail\Admin\MissingVbjehuisMunicipalityMappingEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class MissingVbjehuisMapping implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $recipients = explode(',', config('hoomdossier.admin-emails'));
        foreach ($recipients as $recipient) {
            Mail::to($recipient)->send(new MissingVbjehuisMunicipalityMappingEmail($event->municipality->name));
        }
    }
}
