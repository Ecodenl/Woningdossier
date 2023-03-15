<?php

namespace App\Services\Econobis;

use App\Models\Building;

class EconobisService
{
    public function getPayload(Building $building, ?string $payloadClass = null): array
    {
        $defaultPayload = [
            'account_related' => [
                'building_id' => $building->id,
                'user_id' => $building->user->id,
                'account_id' => $building->user->account_id,
                'contact_id' => $building->user->extra['contact_id'] ?? null,
            ],
        ];

        $payload = [];
        // sometimes there are edge cases, those will be solved in a different manner.
        if (!is_null($payloadClass) && class_exists($payloadClass, true)) {
            $payload = app($payloadClass)->forBuilding($building)->buildPayload();
        }
        return array_merge($defaultPayload, $payload);
    }
}