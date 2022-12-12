<?php

namespace App\Http\ViewComposers;

use App\Models\Step;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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

        if (! $currentStep instanceof Step && Str::startsWith($this->request->route()->getName(), 'cooperation.tool.')) {
            // If the step isn't set, we are in a legacy static expert step and the slug is in the URI
            $slug = str_replace('/tool/', '', $this->request->getRequestUri());
            $currentStep = Step::where('slug', $slug)->first();
        }

        // TODO: Legacy support but perhaps ready for deprecation?
        //if ($currentStep instanceof Step) {
        //    $currentStep->load(['questionnaires' => function ($query) use ($cooperation) {
        //        $query->active()->where('cooperation_id', $cooperation->id)->orderByPivot('order');
        //    }]);
        //}

        $view->with(compact('currentStep'));
    }
}
