<?php

namespace App\Http\Controllers\Cooperation\Frontend\Tool;

use App\Helpers\StepHelper;
use App\Http\Controllers\Controller;
use App\Models\Cooperation;
use App\Models\Step;
use App\Models\SubStep;

class QuickScanController extends Controller
{
    public function index(Cooperation $cooperation, Step $step, SubStep $subStep)
    {
        // the route will always be matched, however a sub step has to match the step.
        abort_if(!$step->subSteps()->find($subStep->id) instanceof SubStep, 404);


        $total = Step::whereIn('short', StepHelper::QUICK_SCAN_STEP_SHORTS)
            ->leftJoin('sub_steps', 'steps.id', '=', 'sub_steps.step_id')
            ->count();
        $current = $subStep->order;

        return view('cooperation.frontend.tool.quick-scan.index', compact('total', 'current', 'step', 'subStep'));
    }
}
