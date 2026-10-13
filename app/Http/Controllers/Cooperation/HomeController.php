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
use App\Services\BuildingCoachStatusService;
use App\Services\Models\ScanService;
use App\Services\Scans\ScanFlowService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;

class HomeController extends Controller
{
    public function index(Cooperation $cooperation): View
    {
        $building = HoomdossierSession::getBuilding(true);
        $inputSource = HoomdossierSession::getInputSource(true);

        $scans = ScanAvailabilityHelper::availableSimpleScans($building);

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
            'coach' => BuildingCoachStatusService::getCoachToShow($building),
            'appointmentDate' => $building->getAppointmentDate(),

            // SmartTwin will deliver this later. Until it does the dashboard shows the row without a
            // value rather than leaving a hole where one is going to be.
            'estimatedEnergyLabel' => null,

            'dossierLinks' => $this->dossierLinks($building, $scans, $masterInputSource),
            'filesUrl' => $scan instanceof Scan
                ? route('cooperation.frontend.tool.simple-scan.my-plan.media', compact('scan'))
                : null,
        ];
    }

    /**
     * One button per scan the building has. With a single scan there is nothing to choose, so it keeps
     * the plain "go to your dossier"; with both, each names its scan the way the old start screen did.
     *
     * @return array<int, array{label: string, url: string}>
     */
    private function dossierLinks(Building $building, Collection $scans, InputSource $masterInputSource): array
    {
        return $scans
            ->map(function (Scan $scan) use ($building, $scans, $masterInputSource) {
                if ($scans->count() === 1) {
                    $label = __('home.dashboard.building.to-dossier');
                } else {
                    $label = __(
                        app(ScanService::class)->scan($scan)->forBuilding($building)->hasMadeScanProgress()
                            ? 'home.start.buttons.continue'
                            : 'home.start.buttons.start',
                        ['scan' => $scan->getTranslation('name', App::getLocale())],
                    );
                }

                return [
                    'label' => $label,
                    'url' => ScanFlowService::init($scan, $building, $masterInputSource)->resolveInitialUrl(),
                ];
            })
            ->all();
    }
}
