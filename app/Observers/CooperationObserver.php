<?php

namespace App\Observers;

use App\Helpers\Cache\Cooperation as CooperationCache;
use App\Models\Cooperation;
use App\Models\CooperationPreset;
use App\Models\Scan;
use App\Services\CooperationScanService;
use App\Services\Models\CooperationPresetService;

class CooperationObserver
{
    public function saved(Cooperation $cooperation): void
    {
        // The session hands out a cached copy of the cooperation (views get it as $cooperation). Left
        // in place, a change such as switching SmartTwin off would only show once the cache expired.
        CooperationCache::wipe(CooperationCache::CACHE_KEY_FIND, $cooperation->id);
    }

    public function created(Cooperation $cooperation): void
    {
        // Give the cooperation the default quick-scan upon register.
        CooperationScanService::init($cooperation)->syncScan(Scan::QUICK);

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
