<?php

namespace App\Listeners;

use App\Helpers\MappingHelper;
use App\Helpers\Queue;
use App\Mail\Admin\MissingBagMunicipalityMappingEmail;
use App\Services\MappingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class CreateTargetlessMappingForMunicipality implements ShouldQueue
{

    public $queue = Queue::APP_EXTERNAL;

    public MappingService $mappingService;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(MappingService $mappingService)
    {
        $this->mappingService = $mappingService;
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(object $event): void
    {
        $this->mappingService
            ->from($event->municipalityName)
            ->sync([], MappingHelper::TYPE_BAG_MUNICIPALITY);

        $recipients = explode(',', config('hoomdossier.contact.email.admin'));
        foreach ($recipients as $recipient) {
            Mail::to($recipient)->send(new MissingBagMunicipalityMappingEmail($event->municipalityName));
        }
    }
}
