<?php

namespace App\Listeners;

use App\Events\NoMappingFoundForBagMunicipality;
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
     */
    public function handle(NoMappingFoundForBagMunicipality $event): void
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
