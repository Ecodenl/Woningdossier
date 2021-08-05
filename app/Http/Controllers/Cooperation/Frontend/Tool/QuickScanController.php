<?php

namespace App\Http\Controllers\Cooperation\Frontend\Tool;

use App\Helpers\StepHelper;
use App\Http\Controllers\Controller;
use App\Models\Cooperation;
use App\Models\Step;
use App\Models\SubStep;
use Illuminate\Support\Facades\DB;

class QuickScanController extends Controller
{
    public function index(Cooperation $cooperation, Step $step, SubStep $subStep)
    {
        // the route will always be matched, however a sub step has to match the step.
        abort_if(!$step->subSteps()->find($subStep->id) instanceof SubStep, 404);

        $total = Step::whereIn('short', StepHelper::QUICK_SCAN_STEP_SHORTS)
            ->leftJoin('sub_steps', 'steps.id', '=', 'sub_steps.step_id')
            ->count();

        // get the previous step ids.
        $stepIds = DB::table('steps')
            ->select('steps.id')
            ->whereIn('short', StepHelper::QUICK_SCAN_STEP_SHORTS)
            ->where('order', '<', $step->order)
            ->pluck('id')->toArray();

        // now get the sub steps for the previous steps, this way we can sum the max sub step order.
        $summedOrder = DB::table('steps')
            ->whereIn('steps.id', $stepIds)
            ->selectRaw('max(sub_steps.order) as order_sum')
            ->groupBy('sub_steps.step_id')
            ->leftJoin('sub_steps', 'steps.id', '=', 'sub_steps.step_id')
            ->get()->sum('order_sum');

        $current = $subStep->order + $summedOrder;

        return view('cooperation.frontend.tool.quick-scan.index', compact('total', 'current', 'step', 'subStep'));
    }
}
