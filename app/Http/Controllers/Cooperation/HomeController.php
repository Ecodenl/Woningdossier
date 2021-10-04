<?php

namespace App\Http\Controllers\Cooperation;

use App\Helpers\HoomdossierSession;
use App\Helpers\QuickScanHelper;
use App\Http\Controllers\Controller;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Step;
use App\Models\SubStep;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @param  \App\Models\Cooperation  $cooperation
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Cooperation $cooperation)
    {

        $building = HoomdossierSession::getBuilding(true);
        $masterInputSource = InputSource::findByShort('master');

        // If the quick scan is complete, we just redirect to my plan
        if ($building->hasCompletedQuickScan($masterInputSource)) {
            $url = route('cooperation.frontend.tool.quick-scan.my-plan.index');
        } else {
            $mostRecentCompletedSubStep = optional(
                $building->completedSubSteps()
                    ->forInputSource($masterInputSource)
                    ->orderByDesc('created_at')
                    ->first()
            )->subStep;

            $quickScanStepIds = Step::quickScan()
                ->pluck('id')
                ->toArray();

            // get all the completed steps
            $mostRecentCompletedStep = optional(
                $building->completedSteps()
                    ->forInputSource($masterInputSource)
                    ->whereIn('step_id', $quickScanStepIds)
                    ->orderByDesc('created_at')
                    ->first()
            )->step;

            // it could be that there is no completed step yet, in that case we just pick the first one.
            if (! $mostRecentCompletedStep instanceof Step) {
                $mostRecentCompletedStep = Step::quickScan()
                    ->orderBy('order')
                    ->first();
            }

            if ($mostRecentCompletedSubStep instanceof SubStep) {
                // now give the user the uncompleted step, because this is probably where he left of
                $url = QuickScanHelper::getNextStepUrl($mostRecentCompletedStep, $mostRecentCompletedSubStep);
            }

            // it could also be that there is no completed sub step, this will mean it's the user his first
            // time using the tool (yay)
            if (! $mostRecentCompletedSubStep instanceof SubStep) {
                $mostRecentCompletedSubStep = $mostRecentCompletedStep->subSteps()->orderBy('order')->first();

                $url = route('cooperation.frontend.tool.quick-scan.index', [
                    'step' => $mostRecentCompletedStep, 'subStep' => $mostRecentCompletedSubStep
                ]);
            }
        }

        return view('cooperation.home.index', compact('url'));
    }
}
