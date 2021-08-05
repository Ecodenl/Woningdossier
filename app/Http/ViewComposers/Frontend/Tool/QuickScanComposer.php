<?php

namespace App\Http\ViewComposers\Frontend\Tool;

use App\Helpers\StepHelper;
use App\Models\Step;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class QuickScanComposer
{
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function create(View $view)
    {
        $step = $this->request->route('step');
        $subStep = $this->request->route('subStep');

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

        $view->with('current', $current);
        $view->with('total', $total);
    }
}


