<?php

namespace App\Http\Controllers\Cooperation\Frontend\Tool;

use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Jobs\CloneOpposingInputSource;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Notification;
use App\Models\Step;
use App\Models\SubStep;

class QuickScanController extends Controller
{
    public function start(Cooperation $cooperation){
        $subStep = SubStep::ordered()->first();
        $step = $subStep->step;

        return redirect()->route('cooperation.frontend.tool.quick-scan.index', compact('step', 'subStep'));
    }

    public function index(Cooperation $cooperation, Step $step, SubStep $subStep)
    {
        // the route will always be matched, however a sub step has to match the step.
        abort_if(!$step->subSteps()->find($subStep->id) instanceof SubStep, 404);
        $currentInputSource = HoomdossierSession::getInputSource(true);

        $notification = Notification::active()
            ->forBuilding(HoomdossierSession::getBuilding())
            ->forType(CloneOpposingInputSource::class)
            ->forInputSource($currentInputSource)->first();

        return view('cooperation.frontend.tool.quick-scan.index', compact('step', 'subStep', 'notification', 'currentInputSource'));
    }
}
