<?php

namespace App\Services\Econobis\Payloads;

class BuildingStatusPayload extends EconobisPayload
{
    public function buildPayload(): array
    {
        $building = $this->building;

        $mostRecentStatus = $building->getMostRecentBuildingStatus();
        return ['status' => $mostRecentStatus->status->only('id', 'short', 'name')];
    }
}