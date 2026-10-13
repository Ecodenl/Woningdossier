<?php

namespace App\Http\ViewComposers\Frontend\Tool;

use App\Helpers\Blade\RouteLogic;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Helpers\ScanAvailabilityHelper;
use App\Helpers\SmallMeasuresSettingHelper;
use App\Models\InputSource;
use App\Models\Scan;
use App\Models\Step;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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
        $availableScans = $building ? ScanAvailabilityHelper::availableSimpleScans($building) : collect();

        /** @var Scan $scan */
        $scan = $this->request->route('scan')
            ?? $this->scanForBuilding($availableScans)
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
        $view->with('showWoningdossier', $this->showWoningdossier($availableScans));
    }

    /**
     * The scan for a page that has none in its route (think of my account, or the dashboard). A
     * building can have both simple scans, and the dashboard lets the user pick; once they have, the
     * navigation stays in that one. Before that, the first scan the building has: the cooperation's
     * first may well be one this building has switched off, and its dossier would show none of the
     * steps.
     *
     * @param Collection<int, Scan> $available
     */
    private function scanForBuilding(Collection $available): ?Scan
    {
        return $this->chosenScan($available) ?? $available->first();
    }

    /**
     * With both simple scans, the dossier link waits until the user has picked one on the dashboard:
     * before that it would have to guess, and would quietly make the choice for them. On the dashboard
     * itself it stays out even after, so the buttons there are the one way to pick. Someone filling in
     * for another building never gets the dashboard, which leaves them the link as their way in.
     *
     * @param Collection<int, Scan> $available
     */
    private function showWoningdossier(Collection $available): bool
    {
        if ($available->count() < 2 || Hoomdossier::user()?->isFillingToolForOtherBuilding()) {
            return true;
        }

        return ! RouteLogic::inDashboard($this->request->route())
            && $this->chosenScan($available) instanceof Scan;
    }

    /**
     * The scan the user last went into, as long as the building still has it.
     *
     * @param Collection<int, Scan> $available
     */
    private function chosenScan(Collection $available): ?Scan
    {
        $remembered = HoomdossierSession::getScan();

        return $remembered instanceof Scan ? $available->firstWhere('id', $remembered->id) : null;
    }
}
