<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\SmartTwin\EventType;
use App\Models\Building;
use App\Services\SmartTwin\BuildingResolver;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class SmartTwinController
{
    public function store(Request $request, BuildingResolver $resolver): Response
    {
        $payload = $request->json()->all();

        Log::debug('SmartTwin webhook received', $payload);

        $data = $payload['data'] ?? null;

        if (! is_array($data)) {
            Log::warning('SmartTwin webhook missing data key', $payload);
            return response()->noContent();
        }

        // Resolved before the building, because the flow decides which of the account's users — and
        // so which set of buildings — the callback can be about.
        $eventType = EventType::tryFrom($data['EventType'] ?? '');

        if (! $eventType instanceof EventType) {
            Log::warning('SmartTwin webhook: unknown EventType', $data);
            return response()->noContent();
        }

        $building = $resolver->resolve($eventType, $data);

        if (! $building instanceof Building) {
            // The resolver logs why: unknown user, no reachable buildings, or no address match.
            return response()->noContent();
        }

        $building->setSmartTwinCallback($eventType, $data);
        $building->save();

        Log::debug('SmartTwin webhook stored callback for building', [
            'building_id' => $building->getKey(),
            'dossierId'   => $data['DossierId'] ?? null,
        ]);

        return response()->noContent();
    }
}
