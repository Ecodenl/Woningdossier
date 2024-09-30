<?php

namespace App\Listeners;

use App\Helpers\Queue;
use App\Mail\Admin\MissingVbjehuisMunicipalityMappingEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class MissingVbjehuisMapping implements ShouldQueue
{
    public $queue = Queue::APP_EXTERNAL;

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
    public function handle($event): void
    {
        $recipients = explode(',', config('hoomdossier.contact.email.admin'));
        foreach ($recipients as $recipient) {
            Mail::to($recipient)->send(new MissingVbjehuisMunicipalityMappingEmail($event->municipality->name));
        }
    }
}
