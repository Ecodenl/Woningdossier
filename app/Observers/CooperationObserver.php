<?php

namespace App\Observers;

use App\Models\Cooperation;
use App\Services\CooperationScanService;
use Database\Seeders\CooperationMeasureApplicationsTableSeeder;

class CooperationObserver
{
    public function created(Cooperation $cooperation)
    {
        // Give the cooperation the default quick-scan upon register.
        CooperationScanService::init($cooperation)->syncScan('quick-scan');

        $cooperation->cooperationMeasureApplications()->createMany(CooperationMeasureApplicationsTableSeeder::MEASURES);
    }
}
