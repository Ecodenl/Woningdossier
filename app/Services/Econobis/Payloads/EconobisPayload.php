<?php

namespace App\Services\Econobis\Payloads;

use App\Models\Building;
use App\Traits\FluentCaller;

abstract class EconobisPayload implements MustBuildPayload
{
    use FluentCaller;

    public Building $building;

    public function forBuilding(Building $building): self
    {
        $this->building = $building;
        return $this;
    }
}