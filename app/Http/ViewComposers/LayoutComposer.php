<?php

namespace App\Http\ViewComposers;

use App\Models\Scan;
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

    public function create(View $view): void
    {
        $currentStep = $this->request->route('step');

        if (! $currentStep instanceof Step && Str::startsWith($this->request->route()->getName(), 'cooperation.tool.')) {
            // If the step isn't set, we are in a legacy static expert step and the slug is in the URI
            $slug = str_replace('/tool/', '', $this->request->getRequestUri());
            $currentStep = Step::where('slug', $slug)->first();
        }

        $view->with(compact('currentStep'));

        $scan = $this->request->route('scan');
        if (! $scan instanceof Scan && $currentStep instanceof Step) {
            // This currently happens in the expert since the expert doesn't have the scan as parametable in the route
            $scan = $currentStep->scan;
        }

        $view->with(compact('scan'));
    }
}
