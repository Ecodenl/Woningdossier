<?php

namespace App\Http\ViewComposers\Frontend\Layouts\Parts;

use App\Helpers\Blade\RouteLogic;
use App\Helpers\HoomdossierSession;
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
        $cooperation = $this->request->route('cooperation');

        $steps = $this->request->route('scan')->steps()->with(['questionnaires' => function ($query) use ($cooperation) {
            $query->active()->where('cooperation_id', $cooperation->id)->orderByPivot('order');
        }])->get();

        $view->with('steps', $steps);

        $view->with('scan', $this->request->route('scan'));

        $view->with('currentStep', $this->request->route('step'));
        $view->with('building', HoomdossierSession::getBuilding(true));

        if (RouteLogic::inQuestionnaire($this->request->route())) {
            $view->with('currentQuestionnaire', $this->request->route('questionnaire'));
        }
    }
}
