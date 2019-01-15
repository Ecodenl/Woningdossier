<?php

namespace App\Http\ViewComposers;

use App\Models\InputSource;
use App\Models\PrivateMessage;
use App\Models\PrivateMessageView;
use Illuminate\View\View;

class CooperationComposer
{
    public function create(View $view)
    {
        $view->with('cooperation', app()->make('Cooperation'));
        $view->with('cooperationStyle', app()->make('CooperationStyle'));

        $view->with('inputSources', InputSource::orderBy('order', 'desc')->get());
    }
}
