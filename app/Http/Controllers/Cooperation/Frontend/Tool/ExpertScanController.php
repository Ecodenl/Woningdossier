<?php

namespace App\Http\Controllers\Cooperation\Frontend\Tool;

use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Cooperation\Tool\ToolController;
use App\Models\Cooperation;
use App\Models\Step;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ExpertScanController extends ToolController
{
    public function index(Request $request, Cooperation $cooperation, Step $step)
    {
        Log::debug('ExpertScanController::index');

        // here we will have to decide whether we will show a dynamic page or a "static" expert page.
        // the dynamic pages are a "new" thing, so the old pages will have to be refactored to dynamic when the time is ripe (or never)
        $dynamicSteps = ['heating'];
        $building = HoomdossierSession::getBuilding(true);
        if (in_array($step->short, $dynamicSteps)) {
            $step->load('subSteps.toolQuestions');
            $masterInputSource = $this->masterInputSource;

            $toolQuestions = [];
            foreach ($step->subSteps as $subStep) {
                foreach ($subStep->toolQuestions()->orderBy('order')->get() as $toolQuestion) {
                    $toolQuestions[$toolQuestion->id] = $toolQuestion;
                }
            }

            $toolQuestions = new EloquentCollection($toolQuestions);

            return view('cooperation.frontend.tool.expert-scan.index', compact('step', 'masterInputSource', 'building', 'toolQuestions'));
        }

        // at this point the step exists, however wrong url. So we will help them a bit.
        return redirect()->route("cooperation.tool.{$step->short}.index");
    }
}
