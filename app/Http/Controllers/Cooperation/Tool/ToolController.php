<?php

namespace App\Http\Controllers\Cooperation\Tool;

use Illuminate\Http\RedirectResponse;
use App\Events\StepDataHasBeenChanged;
use App\Helpers\StepHelper;
use App\Helpers\Hoomdossier;
use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\Step;
use App\Services\Models\SubStepService;
use App\Services\Scans\ScanFlowService;
use Illuminate\Http\Request;

abstract class ToolController extends Controller
{
    protected ?Step $step;

    protected InputSource $masterInputSource;

    public function __construct(Request $request)
    {
        $slug = str_replace('/tool/', '', $request->getRequestUri());
        // Is null if there is currently no request! This only happens when in the console.
        $this->step = Step::where('slug', $slug)->first();

        $this->masterInputSource = InputSource::master();
    }

    /**
     * Instead of doing the same thing in all expert controllers, localize the logic to here.
     */
    public function completeStore(Step $step, Building $building, InputSource $inputSource): RedirectResponse
    {
        /** @var \App\Models\SubStep $subStep */
        $subStep = $step->subSteps()->first();

        StepHelper::complete($step, $building, $inputSource);
        SubStepService::init()->building($building)->inputSource($inputSource)->subStep($subStep)->complete();
        StepDataHasBeenChanged::dispatch($this->step, $building, Hoomdossier::user(), $inputSource);

        return redirect()->to(
            ScanFlowService::init($step->scan, $building, $inputSource)
                ->forStep($step)
                ->forSubStep($subStep) // Always first as legacy steps only have one, else we get weird URLs
                ->resolveNextUrl()
        );
    }
}
