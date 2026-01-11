<?php

namespace App\Http\ViewComposers\Frontend\Tool;

use App\Helpers\HoomdossierSession;
use App\Helpers\SmallMeasuresSettingHelper;
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
        // Load the first available scan if it's not in the current route (think of my account)
        /** @var Scan $scan */
        $scan = $this->request->route('scan', $cooperation->scans()->where('short', '!=', Scan::EXPERT)->first());
        $scan->load(['steps.subSteps']);

        $building = HoomdossierSession::getBuilding(true);

        // Filter kleine maatregelen step indien niet enabled
        if ($building && ! SmallMeasuresSettingHelper::isEnabledForBuilding($building, $scan)) {
            $scan->setRelation('steps', $scan->steps->filter(fn ($step) => $step->short !== 'small-measures'));
        }

        $view->with('scan', $scan);
        $expertScan = Scan::expert();
        $view->with('expertSteps', Step::forScan($expertScan)->get());
        $view->with('building', $building);
        $view->with('masterInputSource', InputSource::findByShort(InputSource::MASTER_SHORT));
    }
}
