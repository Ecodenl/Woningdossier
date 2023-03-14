<?php

namespace App\Http\ViewComposers\Frontend\Tool;

use App\Helpers\StepHelper;
use App\Models\InputSource;
use App\Models\Questionnaire;
use App\Models\Step;
use App\Models\SubStep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SimpleScanComposer
{
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function create(View $view)
    {
        $scan = $this->request->route('scan');
        $step = $this->request->route('step');
        $subStep = $this->request->route('subStep');
        $questionnaire = $this->request->route('questionnaire');
        $cooperation = $this->request->route('cooperation');

        if (is_null($subStep)) {
            $subStep = $step->subSteps()->orderByDesc('order')->first();
        }

        /* RAW:

        SELECT SUM(x.total) FROM (
            SELECT COUNT(*) as total FROM sub_steps AS ss
            LEFT JOIN steps AS s ON ss.step_id = s.id
            WHERE s.scan_id = 2

            UNION

            SELECT COUNT(*) as total FROM questionnaires AS q
            LEFT JOIN questionnaire_step AS qs ON q.id = qs.questionnaire_id
            LEFT JOIN steps AS s ON qs.step_id = s.id
            WHERE s.scan_id = 2 AND q.is_active = 1 AND q.cooperation_id = 1
        ) as x;

        */

        // This query counts all active questionnaires for the current questionnaires linked to the current scan's steps.
        $questionnaireCountQuery = DB::table('questionnaires AS q')
            ->selectRaw("COUNT(*) AS total")
            ->leftJoin('questionnaire_step AS qs', 'q.id', '=', 'qs.questionnaire_id')
            ->leftJoin('steps AS s', 'qs.step_id', '=', 's.id')
            ->where('s.scan_id', $scan->id)
            ->where('q.is_active', true)
            ->where('q.cooperation_id', $cooperation->id);

        // This query counts all sub steps related to the steps of the current scan.
        $subStepCountQuery = DB::table('sub_steps AS ss')->selectRaw("COUNT(*) AS total")
            ->leftJoin('steps AS s', 'ss.step_id', '=', 's.id')
            ->where('s.scan_id', $scan->id);

        // Query for the total count of sub steps and active questionnaires, using a union of the previous query.
        // Note the "clone" method; if we don't do that, the subStepCountQuery gets the union attached. We don't
        // want that, as we want to reuse it.
        $total = (int) DB::table($subStepCountQuery->clone()->union($questionnaireCountQuery), 'x')
            ->selectRaw("SUM(x.total) AS total")
            ->first()
            ->total;

        // Get the sum of sub steps and questionnaires we have already passed, by reusing the same queries,
        // now with a max order.
        $summedOrder = (int) DB::table(
            $questionnaireCountQuery->clone()->where('s.order', '<', $step->order)
                ->union($subStepCountQuery->clone()->where('s.order', '<', $step->order)), 'x')
            ->selectRaw('SUM(x.total) AS sum')
            ->first()
            ->sum;

        // So the logic is as follows:
        // The order of sub steps always starts at 0. Therefore, if we're at the 5th question, order will be 4. We
        // increment the order by 1. If we're on a questionnaire, the order isn't reliable so we count the amount
        // of questionnaires for this step, and which one this is (e.g. 2/3). The questionnaire count is added to the
        // sub step order. Then, we add the summed order.  The summed order holds the total sub steps (OF PREVIOUS STEPS)
        // already completed, as well as the questionnaires.

        $orderToAdd = $subStep->order + 1;
        if ($questionnaire instanceof Questionnaire) {
            // Has to be questionnaire
            $questionnairesForStep = $step->questionnaires()->orderByPivot('order')->get();

            foreach ($questionnairesForStep as $qForStep) {
                $orderToAdd++;
                if ($qForStep->id === $questionnaire->id) {
                    break;
                }
            }
        }

        $current = $orderToAdd + $summedOrder;

        $view->with('current', $current);
        $view->with('total', $total);

        // Additional logic to set question answers if a questionnaire is available
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
