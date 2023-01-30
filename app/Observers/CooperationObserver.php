<?php

namespace App\Observers;

use App\Models\Cooperation;
use App\Models\CooperationPreset;
use App\Services\CooperationScanService;
use App\Services\Models\CooperationPresetService;

class CooperationObserver
{
    public function created(Cooperation $cooperation)
    {
        // Give the cooperation the default quick-scan upon register.
        CooperationScanService::init($cooperation)->syncScan('quick-scan');

        $preset = CooperationPreset::findByShort(CooperationPresetService::COOPERATION_MEASURE_APPLICATIONS);

        $cooperation->cooperationMeasureApplications()->createMany(
            CooperationPresetService::init()->forPreset($preset)->getContent()
        );
    }
}
