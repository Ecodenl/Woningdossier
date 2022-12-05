<?php

namespace App\Http\Controllers\Cooperation\Admin;

use App\Events\FillingToolForUserEvent;
use App\Events\ObservingToolForUserEvent;
use App\Helpers\Hoomdossier;
use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\Step;

class ToolController extends Controller
{
    /**
     * Set the sessions and after that redirect them to the tool.
     *
     * @param $buildingId
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function fillForUser(Cooperation $cooperation, Building $building)
    {
        $building->load('user');

        $this->authorize('access-building', $building);

        FillingToolForUserEvent::dispatch($building, Hoomdossier::user());

        $step = Step::findByShort('building-data');

        return redirect()->route('cooperation.frontend.tool.scans.index', [
            'scan' => $step->scan,
            'step' => $step,
            'subStep' => $step->subSteps()->orderBy('order')->first(),
        ]);
    }

    /**
     * Sessions that need to be set so we can let a user observe a building / tool.
     *
     * @param $buildingId
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function observeToolForUser(Cooperation $cooperation, Building $building)
    {
        $building->load('user');

        $this->authorize('access-building', $building);

        ObservingToolForUserEvent::dispatch($building, Hoomdossier::user());

        $step = Step::findByShort('building-data');

        return redirect()->route('cooperation.frontend.tool.quick-scan.index', [
            'scan' => $step->scan,
            'step' => $step,
            'subStep' => $step->subSteps()->orderBy('order')->first(),
        ]);
    }
}
