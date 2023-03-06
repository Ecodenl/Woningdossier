<?php

namespace App\Services\Econobis\Payloads;

class AppointmentDatePayload extends EconobisPayload
{
    public function buildPayload(): array
    {
        $building = $this->building;

        $mostRecentStatus = $building->getMostRecentBuildingStatus();
        return ['appointment_date' => $mostRecentStatus->appointment_date];
    }
}