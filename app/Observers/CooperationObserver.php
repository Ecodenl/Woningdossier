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
        if ($preset instanceof CooperationPreset) {
            $service = CooperationPresetService::init()->forPreset($preset);
            $content = $service->getContent();

            foreach ($content as $measureContent) {
                $relations = $measureContent['relations'] ?? [];
                unset($measureContent['relations']);
                $model = $cooperation->cooperationMeasureApplications()->create($measureContent);

                if (is_array($relations)) {
                    $service->forModel($model)->handleRelations($relations);
                }
            }
        }
    }
}
