<?php

namespace App\Http\ViewComposers\Frontend\Tool;

use App\Helpers\HoomdossierSession;
use App\Helpers\ScanAvailabilityHelper;
use App\Helpers\SmallMeasuresSettingHelper;
use App\Models\Building;
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
        $building = HoomdossierSession::getBuilding(true);

        /** @var Scan $scan */
        $scan = $this->request->route('scan')
            ?? ($building ? $this->scanForBuilding($building) : null)
            ?? $cooperation->scans()->where('short', '!=', Scan::EXPERT)->first();
        $scan->load(['steps.subSteps']);

        // Filter scan indien niet beschikbaar voor dit building
        if ($building && ! ScanAvailabilityHelper::isAvailableForBuilding($building, $scan)) {
            $scan->setRelation('steps', collect());
        }

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

    /**
     * The scan for a page that has none in its route (think of my account, or the dashboard). A
     * building can have both simple scans, and the dashboard lets the user pick; once they have, the
     * navigation stays in that one. Before that, the first scan the building has: the cooperation's
     * first may well be one this building has switched off, and its dossier would show none of the
     * steps.
     */
    private function scanForBuilding(Building $building): ?Scan
    {
        $available = ScanAvailabilityHelper::availableSimpleScans($building);

        return $available->firstWhere('id', HoomdossierSession::getScan()?->id) ?? $available->first();
    }
}
