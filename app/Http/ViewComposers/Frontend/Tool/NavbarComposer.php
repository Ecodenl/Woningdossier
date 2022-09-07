<?php

namespace App\Http\ViewComposers\Frontend\Tool;

use App\Helpers\HoomdossierSession;
use App\Models\InputSource;
use App\Models\Step;
use Illuminate\View\View;

class NavbarComposer
{
    public function create(View $view)
    {
        $view->with('expertSteps', Step::expert()->get());
        $view->with('building', HoomdossierSession::getBuilding(true));
        $view->with('masterInputSource', InputSource::findByShort(InputSource::MASTER_SHORT));
    }
}
