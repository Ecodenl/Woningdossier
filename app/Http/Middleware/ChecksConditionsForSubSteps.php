<?php

namespace App\Http\Middleware;

use App\Helpers\HoomdossierSession;
use App\Helpers\QuickScanHelper;
use App\Models\InputSource;
use App\Models\ToolQuestion;
use Closure;

class ChecksConditionsForSubSteps
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $subStep = $request->route('subStep');
        if (!empty($subStep->conditions)) {
            $building = HoomdossierSession::getBuilding(true);
            $masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
            // we will collect the answers, this way we can query on the collection with the $conditions array.
            $answers = collect();
            $conditions = $subStep->conditions;
            foreach ($conditions as $condition) {
                $toolQuestion = ToolQuestion::findByShort($condition['column']);
                // set the answers inside the collection
                $answers->push([$condition['column'] =>  $building->getAnswer($masterInputSource, $toolQuestion)]);
            }


            // first check if the user actually gave an answer, which is mandatory but better to double check
            if ($answers->filter()->isNotEmpty()) {
                foreach ($conditions as $condition) {
                    $answers = $answers->where($condition['column'], $condition['operator'], $condition['value']);
                }

                // if there is no match we should go to the next step.
                if ($answers->isEmpty()) {
                    return redirect()->to(QuickScanHelper::getNextStepUrl($request->route('step'), $subStep));
                }
            }
        }
        return $next($request);
    }
}
