<?php

namespace App\Http\ViewComposers\Cooperation\Admin\Layouts;

use App\Helpers\HoomdossierSession;
use App\Models\InputSource;
use App\Models\Scan;
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

    public function create(View $view): void
    {
        /** @var \App\Models\Cooperation $cooperation */
        $cooperation = $this->request->route('cooperation');

        $view->with(
            'scans',
            $cooperation->scans()->where('short', '!=', Scan::EXPERT)->get()
        );
    }
}
