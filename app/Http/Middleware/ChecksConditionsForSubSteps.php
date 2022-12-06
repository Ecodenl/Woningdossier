<?php

namespace App\Http\Middleware;

use App\Helpers\ExampleBuildingHelper;
use App\Helpers\HoomdossierSession;
use App\Helpers\QuickScanHelper;
use App\Models\CompletedSubStep;
use App\Models\InputSource;
use App\Models\SubStep;
use App\Services\Scans\ScanFlowService;
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

        $scan = $request->route('scan');
        /** @var SubStep $subStep */
        $subStep = $request->route('subStep');
        $step = $request->route('step');

        $returnToNextStep = $request->user()->cannot('show', [$subStep, $building]);

        if ($returnToNextStep) {
            // the current sub step cant be showed, resolve the next url.
            $url = ScanFlowService::init($scan, $building, HoomdossierSession::getInputSource(true))
                ->forStep($step)
                ->forSubStep($subStep)
                ->resolveNextUrl();
            return redirect()->to($url);

        }

        // We can show this step according to the sub step conditionals, but have we answered the example building yet?
        if (! in_array($subStep->getTranslation('slug', 'nl'), ExampleBuildingHelper::RELEVANT_SUB_STEPS)) {
            // Not an example building sub step, let's check...

            $masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
            foreach (ExampleBuildingHelper::RELEVANT_SUB_STEPS as $subStepSlug) {
                $subStep = $step->subSteps()->where('slug->nl', $subStepSlug)->first();
                // If valid sub step and showable (could be unanswerable)
                if ($subStep instanceof SubStep && $request->user()->can('show', [$subStep, $building])) {
                    if (! $building->completedSubSteps()->forInputSource($masterInputSource)->where('sub_step_id', $subStep->id)->first() instanceof CompletedSubStep) {
                        // Not answered, redirect back
                        return redirect()->route('cooperation.frontend.tool.simple-scan.index', [
                            'scan' => $request->route('scan'),
                            'step' => $step,
                            'subStep' => $subStep,
                        ]);
                    }
                }
            }
        }

        return $next($request);
    }
}
