<?php

namespace App\Http\ViewComposers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class LayoutComposer
{
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function create(View $view)
    {
        $cooperation = $this->request->route('cooperation');

        $currentStep = $this->request->route('step');
        $currentStep->load(['questionnaires' => function ($query) use ($cooperation) {
            $query->active()->where('cooperation_id', $cooperation->id)->orderByPivot('order');
        }]);

        $view->with(compact('currentStep'));
    }
}
