<?php

namespace App\Services\SmartTwin;

use App\Enums\SmartTwin\EventType;
use App\Models\Building;

class SmartTwinService
{
    public function processResults(Building $building, array $results, EventType $eventType): void
    {
        // TODO: process $results for $building based on $eventType
    }
}
