<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Events\StepDataHasBeenChanged;
use App\Helpers\StepHelper;
use App\Helpers\Hoomdossier;
use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\Step;
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
        StepHelper::complete($step, $building, $inputSource);
        $building->update([
            'has_answered_expert_question' => true,
        ]);
        StepDataHasBeenChanged::dispatch($this->step, $building, Hoomdossier::user());

        $nextStep = StepHelper::getNextStep($building, $inputSource, $this->step);
        $url = $nextStep['url'];

        if (! empty($nextStep['tab_id'])) {
            $url .= '#' . $nextStep['tab_id'];
        }

        return redirect($url);
    }
}
