<?php

namespace App\Http\ViewComposers\Frontend\Tool;

use App\Helpers\StepHelper;
use App\Models\InputSource;
use App\Models\Questionnaire;
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

        // TODO: Most likely this will make place for a questionnaire to add to the total count / step set
        if (is_null($subStep)) {
            $subStep = $step->subSteps()->orderByDesc('order')->first();
        }

        $total = Step::quickScan()
            ->leftJoin('sub_steps', 'steps.id', '=', 'sub_steps.step_id')
            ->count();

        // Get all the IDs of previous steps. Currently there are a total of 4 steps, so
        // we can have a maximum of 3 step IDs
        $stepIds = DB::table('steps')
            ->select('steps.id')
            ->whereIn('short', StepHelper::QUICK_SCAN_STEP_SHORTS)
            ->where('order', '<', $step->order)
            ->pluck('id')->toArray();

        // Now get the sub steps for the previous steps. This way we can sum the max sub step order.
        $summedOrder = DB::table('steps')
            ->whereIn('steps.id', $stepIds)
            ->selectRaw('max(sub_steps.order) + 1 as order_sum')
            ->groupBy('sub_steps.step_id')
            ->leftJoin('sub_steps', 'steps.id', '=', 'sub_steps.step_id')
            ->get()->sum('order_sum');

        // So the logic is as follows:
        // The order always starts at 0. Therefore, if we're at the 5th question, order will be 4. We increment
        // the order by 1, and then add the summed order. The summed order consists of the maximum order + 1, for
        // the same logic as already defined; The summed order holds the total sub steps (OF PREVIOUS STEPS) already
        // completed.
        $current = $subStep->order + 1 + $summedOrder;

        $view->with('current', $current);
        $view->with('total', $total);

        // Additional logic to set question answers if a questionnaire is available
        $questionnaire = $this->request->route('questionnaire');
        if ($questionnaire instanceof Questionnaire) {
            $masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);

            $questionnaire->load(['questions' => function ($query) use ($masterInputSource) {
                $query->orderBy('order')
                    ->with(['questionAnswers' => function ($query) use ($masterInputSource) {
                        $query->where('building_id', \App\Helpers\HoomdossierSession::getBuilding())
                            ->forInputSource($masterInputSource);
                    }, 'questionAnswersForMe' => function ($query) use ($masterInputSource) {
                        $query->where('input_source_id', '!=', $masterInputSource->id);
                    }]);
            }]);

            $view->with('questionnaire', $questionnaire);
        }
    }
}
