<?php

namespace App\Http\ViewComposers\Frontend\Tool;

use App\Models\Step;
use Illuminate\View\View;

class NavbarComposer
{
    public function create(View $view)
    {
        $view->with('expertSteps', Step::expert()->get());
    }
}
