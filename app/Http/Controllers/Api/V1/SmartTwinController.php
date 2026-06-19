<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Models\BuildingSettingHelper;
use App\Models\Building;
use App\Models\BuildingSetting;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class SmartTwinController
{
    public function store(Request $request): Response
    {
        $payload = $request->json()->all();

        Log::debug('SmartTwin webhook received', $payload);

        $data = $payload['data'] ?? null;

        if (! is_array($data)) {
            Log::warning('SmartTwin webhook missing data key', $payload);
            return response()->noContent();
        }

        $dossierId = $data['DossierId'] ?? null;

        if (! $dossierId) {
            Log::warning('SmartTwin webhook missing DossierId', $data);
            return response()->noContent();
        }

        $building = BuildingSetting::forShort(BuildingSettingHelper::SHORT_SMARTTWIN_DOSSIER_ID)
            ->where('value', $dossierId)
            ->first()
            ?->building;

        if (! $building instanceof Building) {
            Log::warning('SmartTwin webhook: no building found for DossierId', ['dossierId' => $dossierId]);
            return response()->noContent();
        }

        $callbacks = $building->getSmartTwinCallbacks();
        $callbacks[] = $data;

        $building->smarttwin_callback = $callbacks;
        $building->save();

        Log::debug('SmartTwin webhook stored callback for building', [
            'building_id' => $building->getKey(),
            'dossierId'   => $dossierId,
        ]);

        return response()->noContent();
    }
}
