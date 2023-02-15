<?php

namespace App\Listeners;

use App\Helpers\MappingHelper;
use App\Mail\Admin\NoMappingFoundForBagMunicipalityEmail;
use App\Services\MappingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class CreateTargetlessMappingForMunicipality implements ShouldQueue
{
    public $queue = 'default';

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
    public function handle($event)
    {
        $this->mappingService
            ->from($event->municipalityName)
            ->sync([], MappingHelper::TYPE_MUNICIPALITY);

        $recipients = explode(',', config('hoomdossier.admin-emails'));
        foreach ($recipients as $recipient) {
            Mail::to($recipient)->send(new NoMappingFoundForBagMunicipalityEmail($event->municipalityName));
        }
    }
}
