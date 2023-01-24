<?php

namespace App\Http\Controllers\Cooperation\Admin;

use App\Events\FillingToolForUserEvent;
use App\Events\ObservingToolForUserEvent;
use App\Helpers\Hoomdossier;
use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Scan;
use App\Models\Step;
use App\Services\Scans\ScanFlowService;

class ToolController extends Controller
{
    /**
     * Set the sessions and after that redirect them to the tool.
     *
     * @param $buildingId
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function fillForUser(Cooperation $cooperation, Building $building, Scan $scan)
    {
        $building->load('user');
        $this->authorize('access-building', $building);

        FillingToolForUserEvent::dispatch($building, Hoomdossier::user());

        return redirect()->to(
            ScanFlowService::init($scan, $building, InputSource::findByShort(InputSource::MASTER_SHORT))->resolveInitialUrl()
        );
    }

    /**
     * Sessions that need to be set so we can let a user observe a building / tool.
     *
     * @param $buildingId
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function observeToolForUser(Cooperation $cooperation, Building $building, Scan $scan)
    {
        $building->load('user');

        $this->authorize('access-building', $building);

        ObservingToolForUserEvent::dispatch($building, Hoomdossier::user());

        return redirect()->to(
            ScanFlowService::init($scan, $building, InputSource::findByShort(InputSource::MASTER_SHORT))->resolveInitialUrl()
        );
    }
}
