<?php

namespace App\Http\ViewComposers;

use App\Models\InputSource;
use Illuminate\View\View;

class CooperationComposer
{
    public function create(View $view)
    {
        $view->with('cooperation', app()->make('Cooperation'));
        $view->with('cooperationStyle', app()->make('CooperationStyle'));
        $view->with('inputSources', \App\Helpers\Cache\InputSource::getOrdered());
    }
}
