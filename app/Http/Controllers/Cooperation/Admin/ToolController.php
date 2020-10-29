<?php

namespace App\Http\Controllers\Cooperation\Admin;

use App\Events\FillingToolForUserEvent;
use App\Events\ObservingToolForUserEvent;
use App\Helpers\Hoomdossier;
use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\User;

class ToolController extends Controller
{
    /**
     * Set the sessions and after that redirect them to the tool.
     *
     * @param $buildingId
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function fillForUser(Cooperation $cooperation, $buildingId)
    {
        // The building the coach wants to edit
        $building = Building::find($buildingId)->load('user');

        $this->authorize('access-building', $building);

        FillingToolForUserEvent::dispatch($building, Hoomdossier::user());

        return redirect()->route('cooperation.tool.index');
    }

    /**
     * Sessions that need to be set so we can let a user observe a building / tool.
     *
     * @param $buildingId
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function observeToolForUser(Cooperation $cooperation, $buildingId)
    {
        // The building the user wants to observe
        $building = Building::find($buildingId)->load('user');

        $this->authorize('access-building', $building);

        ObservingToolForUserEvent::dispatch($building, Hoomdossier::user());

        return redirect()->route('cooperation.tool.index');
    }
}
