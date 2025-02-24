<?php

namespace App\Http\ViewComposers;

use App\Models\Cooperation;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class CooperationComposer
{
    private ?Cooperation $cooperation = null;
    private ?Collection $inputSources = null;

    public function create(View $view): void
    {
        if (is_null($this->cooperation)) {
            $this->cooperation = app()->make('Cooperation');
        }
        $view->with('cooperation', $this->cooperation);

        if (is_null($this->inputSources)) {
            $this->inputSources = \App\Helpers\Cache\InputSource::getOrdered();
        }
        $view->with('inputSources', $this->inputSources);
    }
}
