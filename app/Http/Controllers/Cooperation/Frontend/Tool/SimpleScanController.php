<?php

namespace App\Http\Controllers\Cooperation\Frontend\Tool;

use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Jobs\CloneOpposingInputSource;
use App\Models\Cooperation;
use App\Models\Scan;
use App\Models\Step;
use App\Models\SubStep;
use App\Services\Models\NotificationService;

class SimpleScanController extends Controller
{
    public function index(Cooperation $cooperation, Scan $scan, Step $step, SubStep $subStep)
    {
        // the route will always be matched, however a sub step has to match the step.
        abort_if(! $step->subSteps()->find($subStep->id) instanceof SubStep, 404);
        $currentInputSource = HoomdossierSession::getInputSource(true);

        $activeNotification = NotificationService::init()
            ->forInputSource($currentInputSource)
            ->forBuilding(HoomdossierSession::getBuilding(true))
            ->setType(CloneOpposingInputSource::class)
            ->isActive();

        return view("cooperation.frontend.tool.simple-scan.index", compact('scan', 'step', 'subStep', 'activeNotification', 'currentInputSource'));
    }

}
