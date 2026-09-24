<?php

namespace App\Http\Controllers\Cooperation;

use Illuminate\View\View;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Helpers\ScanAvailabilityHelper;
use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\BuildingFeature;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Scan;
use App\Models\User;
use App\Services\BuildingCoachStatusService;
use App\Services\Scans\ScanFlowService;
use Illuminate\Support\Collection;

class HomeController extends Controller
{
    public function index(Cooperation $cooperation): View
    {
        $building = HoomdossierSession::getBuilding(true);
        $inputSource = HoomdossierSession::getInputSource(true);

        $scans = Scan::simpleScans()->get()
            ->filter(fn ($scan) => ScanAvailabilityHelper::isAvailableForBuilding($building, $scan))
            ->values();

        $data = compact('building', 'inputSource', 'scans');

        // The dashboard only exists in SmartTwin mode; without it this route still shows the old
        // start screen, which needs none of the below.
        if (Hoomdossier::hasEnabledSmartTwinCalls() && $building instanceof Building) {
            $data += $this->dashboardData($building, $scans);
        }

        return view('cooperation.home.index', $data);
    }

    /**
     * @return array<string, mixed>
     */
    private function dashboardData(Building $building, Collection $scans): array
    {
        $masterInputSource = InputSource::master();

        // Explicitly the master row. Left to the global scope this reads whatever the session's
        // input source is, which for a resident is their own -- and the figures on this tile come
        // from BAG and EP-Online, which write to master. The resident would see an empty tile.
        $features = $building->buildingFeatures()
            ->allInputSources()
            ->forInputSource($masterInputSource)
            ->with(['buildingType', 'energyLabel'])
            ->first();

        // The tiles link into the dossier and the file overview, both of which hang off a scan.
        $scan = $scans->first();

        return [
            'features' => $features instanceof BuildingFeature ? $features : null,
            'coach' => $this->coachFor($building),
            'appointmentDate' => $building->getAppointmentDate(),

            // SmartTwin will deliver this later. Until it does the dashboard shows the row without a
            // value rather than leaving a hole where one is going to be.
            'estimatedEnergyLabel' => null,

            'dossierUrl' => $scan instanceof Scan
                ? ScanFlowService::init($scan, $building, $masterInputSource)->resolveInitialUrl()
                : null,
            'filesUrl' => $scan instanceof Scan
                ? route('cooperation.frontend.tool.simple-scan.my-plan.media', compact('scan'))
                : null,
        ];
    }

    /**
     * A building can have several coaches attached over time; the design shows one, so we take the
     * one attached most recently.
     */
    private function coachFor(Building $building): ?User
    {
        return BuildingCoachStatusService::getConnectedCoachesByBuilding($building, true)
            ->last()
            ?->coach;
    }
}
