<?php

namespace App\Observers;

use App\Models\Cooperation;
use Database\Seeders\CooperationMeasureApplicationsTableSeeder;

class CooperationObserver
{
    public function created(Cooperation $cooperation)
    {
        $cooperation->cooperationMeasureApplications()->createMany(CooperationMeasureApplicationsTableSeeder::MEASURES);
    }
}
