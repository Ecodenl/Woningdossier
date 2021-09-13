<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation\CooperationAdmin;

use App\Helpers\Cache\Cooperation as CooperationCache;
use App\Helpers\StepHelper;
use App\Http\Controllers\Controller;
use App\Models\Cooperation;
use App\Models\Step;
use Illuminate\Http\Request;

class CooperationMeasureApplicationController extends Controller
{
    public function index(Cooperation $cooperation)
    {
        $cooperationMeasureApplications = $cooperation->cooperationMeasureApplications;

        return view('cooperation.admin.cooperation.cooperation-admin.cooperation-measure-applications.index', compact('cooperationMeasureApplications'));
    }
}
