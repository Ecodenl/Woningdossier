<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;

class CooperationComposer
{
    private $cooperation;
    private $cooperationStyle;
    private $inputSources;

    public function create(View $view)
    {
        if (is_null($this->cooperation)){
            $this->cooperation = app()->make('Cooperation');
        }
        $view->with('cooperation', $this->cooperation);

        if (is_null($this->cooperationStyle)){
            $this->cooperationStyle = app()->make('CooperationStyle');
        }
        $view->with('cooperationStyle', $this->cooperationStyle);

        if (is_null($this->inputSources)){
            $this->inputSources = \App\Helpers\Cache\InputSource::getOrdered();
        }
        $view->with('inputSources', $this->inputSources);
    }
}