<?php

namespace App\Http\Controllers\Cooperation;

use App\Helpers\HoomdossierSession;
use App\Helpers\QuickScanHelper;
use App\Helpers\StepHelper;
use App\Http\Controllers\Controller;
use App\Models\Cooperation;
use App\Models\Step;

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

        // get all the completed steps
        $mostRecentCompletedStep = $building->completedSteps()->whereIn(
            'step_id',
            Step::whereIn('short', StepHelper::QUICK_SCAN_STEP_SHORTS)->pluck('id')->toArray()
        )->orderByDesc('created_at')->first()->step;

        $mostRecentCompletedSubStep = $building->completedSubSteps()->orderByDesc('created_at')->first()->subStep;


        return view('cooperation.home.index', compact('mostRecentCompletedStep', 'mostRecentCompletedSubStep'));
    }
}
