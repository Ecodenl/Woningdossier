<?php

namespace App\Http\Controllers\Cooperation\Frontend\Tool;

use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Cooperation\Tool\ToolController;
use App\Jobs\CloneOpposingInputSource;
use App\Models\Cooperation;
use App\Models\Notification;
use App\Models\Scan;
use App\Models\Step;
use App\Models\SubStep;
use App\Services\Scans\ExpertScanService;
use Illuminate\Support\Facades\Log;

class ExpertScanController extends ToolController
{
    public function index(Cooperation $cooperation, Scan $scan, Step $step)
    {
        Log::debug('ExpertScanController::index');

        // here we will have to decide whether we will show a dynamic page or a "static" expert page.
        // the dynamic pages are a "new" thing, so the old pages will have to be refactored to dynamic when the time is ripe (or never)
        $dynamicSteps = ['heating'];
        $building = HoomdossierSession::getBuilding(true);
        if (in_array($step->short, $dynamicSteps)) {
            $step->load('subSteps.toolQuestions');
            $masterInputSource = $this->masterInputSource;

            return view('cooperation.frontend.tool.expert-scan.index', compact('step', 'masterInputSource', 'building'));
        }

        return (new ExpertScanService($step, $building, HoomdossierSession::getInputSource(true)))
            ->view();
    }
}
