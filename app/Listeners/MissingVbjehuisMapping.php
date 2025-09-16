<?php

namespace App\Listeners;

use App\Events\NoMappingFoundForVbjehuisMunicipality;
use App\Helpers\Queue;
use App\Mail\Admin\MissingVbjehuisMunicipalityMappingEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class MissingVbjehuisMapping implements ShouldQueue
{
    public $queue = Queue::APP_EXTERNAL;

    /**
     * Handle the event.
     */
    public function handle(NoMappingFoundForVbjehuisMunicipality $event): void
    {
        $recipients = array_filter(explode(',', config('hoomdossier.contact.email.admin')));
        if (! empty($recipients)) {
            foreach ($recipients as $recipient) {
                Mail::to($recipient)->send(new MissingVbjehuisMunicipalityMappingEmail($event->municipality->name));
            }
        }
    }
}
