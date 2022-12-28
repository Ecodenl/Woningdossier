<?php

namespace App\Observers;

use App\Models\MeasureApplication;

class MeasureApplicationObserver
{
    public function saved(MeasureApplication $measureApplication)
    {
        MeasureApplication::clearShortCache($measureApplication->short);
    }
}
