<?php

namespace App\Http\ViewComposers\Frontend\Layouts\Parts;

use App\Helpers\Blade\RouteLogic;
use App\Helpers\HoomdossierSession;
use App\Helpers\StepHelper;
use App\Models\Step;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubNavComposer
{
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function create(View $view)
    {
        $view->with('steps',
            Step::whereIn('short', StepHelper::QUICK_SCAN_STEP_SHORTS)->get()
        );

        $view->with('currentStep', $this->request->route('step'));
        $view->with('building', HoomdossierSession::getBuilding(true));

        if (RouteLogic::inQuestionnaire($this->request->route())) {
            $view->with('currentQuestionnaire', $this->request->route('questionnaire'));
        }
    }
}