<?php

namespace App\Http\Controllers\Cooperation\Frontend\Tool\QuickScan;

use App\Helpers\HoomdossierSession;
use App\Models\Building;
use App\Models\Step;
use App\Models\SubStep;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MyPlanController extends Controller
{
    public function index()
    {
        /** @var Building $building */
        $building = HoomdossierSession::getBuilding(true);

        $irrelevantSteps = $building->completedSteps()->pluck('step_id')->toArray();
        $firstIncompleteStep = Step::quickScan()
            ->whereNotIn('id', $irrelevantSteps)
            ->orderBy('order')
            ->first();

        // There are incomplete steps left, set the sub step
        if ($firstIncompleteStep instanceof Step) {
            $firstIncompleteSubStep = $firstIncompleteStep->subSteps()->orderBy('order')->first();

            if ($firstIncompleteSubStep instanceof SubStep) {
                return redirect()->route('cooperation.frontend.tool.quick-scan.index', [
                    'step' => $firstIncompleteStep,
                    'subStep' => $firstIncompleteSubStep,
                ]);
            }
        }

        return view('cooperation.frontend.tool.quick-scan.my-plan.index');
    }
}
