<?php

namespace App\Http\Middleware;

use App\Helpers\ExampleBuildingHelper;
use App\Helpers\HoomdossierSession;
use App\Helpers\QuickScanHelper;
use App\Models\CompletedSubStep;
use App\Models\InputSource;
use App\Models\SubStep;
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
        $building = HoomdossierSession::getBuilding(true);

        /** @var SubStep $subStep */
        $subStep = $request->route('subStep');

        $returnToNextStep = $request->user()->cannot('show', [$subStep, $building]);

        if ($returnToNextStep) {
            // this indeed only covers the next step
            return redirect()->to(QuickScanHelper::getNextStepUrl($request->route('step'), $subStep));
        }

        // We can show this step according to the sub step conditionals, but have we answered the example building yet?
        if (! in_array($subStep->getTranslation('slug', 'nl'), ExampleBuildingHelper::RELEVANT_SUB_STEPS)) {
            // Not an example building sub step, let's check...

            $masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
            foreach (ExampleBuildingHelper::RELEVANT_SUB_STEPS as $subStepSlug) {
                $subStep = SubStep::where('slug->nl', $subStepSlug)->first();
                // If valid sub step and showable (could be unanswerable)
                if ($subStep instanceof SubStep && $request->user()->can('show', [$subStep, $building])) {
                    if (! $building->completedSubSteps()->forInputSource($masterInputSource)->where('sub_step_id', $subStep->id)->first() instanceof CompletedSubStep) {
                        // Not answered, redirect back
                        return redirect()->route('cooperation.frontend.tool.quick-scan.index', [
                            'step' => $subStep->step,
                            'subStep' => $subStep,
                        ]);
                    }
                }
            }
        }

        return $next($request);
    }
}
