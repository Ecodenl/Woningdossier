<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation\CooperationAdmin;

use App\Helpers\Cache\Cooperation as CooperationCache;
use App\Helpers\StepHelper;
use App\Http\Controllers\Controller;
use App\Models\Cooperation;
use App\Models\Step;
use Illuminate\Http\Request;

class StepController extends Controller
{
    public function index(Cooperation $cooperation)
    {
        // Route is disabled. Die if they somehow still manage to get here
        die();

        // since the general-data always need to be the first step and is always needed
        $steps = $cooperation
            ->steps()
            ->orderBy('cooperation_steps.order')
            ->where('short', '!=', 'general-data')
            ->expert()
            ->whereNull('parent_id')
            ->get();

        return view('cooperation.admin.cooperation.cooperation-admin.steps.index', compact('steps'));
    }
}
