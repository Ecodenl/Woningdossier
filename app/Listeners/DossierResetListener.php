<?php

namespace App\Listeners;

use App\Events\DossierResetPerformed;
use App\Services\ToolSettingService;

class DossierResetListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param object $event
     *
     * @return void
     */
    public function handle(DossierResetPerformed $event)
    {
        ToolSettingService::clearChanged($event->building);
    }
}
