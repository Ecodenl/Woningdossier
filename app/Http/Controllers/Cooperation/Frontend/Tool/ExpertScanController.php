<?php

namespace App\Http\Controllers\Cooperation\Frontend\Tool;

use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Cooperation\Tool\ToolController;
use App\Models\Cooperation;
use App\Models\Scan;
use App\Models\Step;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ExpertScanController extends ToolController
{
    public function index(Request $request, Cooperation $cooperation, Step $step)
    {
        $scan = Scan::findByShort(Scan::EXPERT);
        // Abort if not an expert step; no route binding here yet
        abort_if($step->scan->id !== $scan->id, 404);

        Log::debug('ExpertScanController::index');

        // here we will have to decide whether we will show a dynamic page or a "static" expert page.
        // the dynamic pages are a "new" thing, so the old pages will have to be refactored to dynamic when the time is ripe (or never)
        $dynamicSteps = ['heating'];
        $building = HoomdossierSession::getBuilding(true);
        if (in_array($step->short, $dynamicSteps)) {
            Log::debug('ExpertScanController::index found dynamic step ' . $step->short);
            $step->load('subSteps.toolQuestions');
            $masterInputSource = $this->masterInputSource;

            return view('cooperation.frontend.tool.expert-scan.index', compact('step', 'masterInputSource', 'building'));
        }

        $redirectSteps = [
            'heater' => 'heating',
            'heat-pump' => 'heating',
        ];
        if (array_key_exists($step->short, $redirectSteps)) {
            Log::debug('ExpertScanController::index found redirect step ' . $step->short);

            $step = Step::findByShort($redirectSteps[$step->short]);
            return redirect()->route('cooperation.frontend.tool.expert-scan.index', compact('step'));
        }

        Log::debug('ExpertScanController::index found static step ' . $step->short);
        // at this point the step exists, however wrong url. So we will help them a bit.
        return redirect()->route("cooperation.tool.{$step->short}.index");
    }
}
