<?php

namespace App\Http\ViewComposers\Cooperation\Admin\Layouts;

use App\Helpers\HoomdossierSession;
use App\Models\InputSource;
use App\Models\Step;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NavbarComposer
{
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function create(View $view)
    {
        $view->with(
            'scans',
            $this->request->route('cooperation')->scans()->where('short', '!=', 'expert-scan')->get()
        );
    }
}
