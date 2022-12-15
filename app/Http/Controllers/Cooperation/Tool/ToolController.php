<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Events\StepDataHasBeenChanged;
use App\Helpers\StepHelper;
use App\Helpers\Hoomdossier;
use App\Helpers\SubStepHelper;
use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\Step;
use App\Services\Models\SubStepService;
use App\Services\Scans\ScanFlowService;
use Illuminate\Http\Request;

class ToolController extends Controller
{
    /**
     * @var Step
     */
    protected $step;

    protected InputSource $masterInputSource;

    public function __construct(Request $request)
    {
        $slug = str_replace('/tool/', '', $request->getRequestUri());
        $this->step = Step::where('slug', $slug)->first();

        $this->masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
    }

    /**
     * Instead of doing the same thing in all expert controllers, localize the logic to here.
     *
     * @param  \App\Models\Step  $step
     * @param  \App\Models\Building  $building
     * @param  \App\Models\InputSource  $inputSource
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function completeStore(Step $step, Building $building, InputSource $inputSource)
    {
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
