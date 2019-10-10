<?php

namespace App\Http\Controllers\Cooperation\Admin;

use App\Events\FillingToolForUserEvent;
use App\Events\ObservingToolForUserEvent;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ToolController extends Controller
{

    /**
     * Set the sessions and after that redirect them to the tool.
     *
     * @param Cooperation $cooperation
     * @param $buildingId
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function fillForUser(Cooperation $cooperation, $buildingId)
    {
        // The building the coach wants to edit
        $building = Building::find($buildingId);

        FillingToolForUserEvent::dispatch($building, Hoomdossier::user());

        return redirect()->route('cooperation.tool.index');
    }

    /**
     * Sessions that need to be set so we can let a user observe a building / tool
     *
     * @param Cooperation $cooperation
     * @param $buildingId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function observeToolForUser(Cooperation $cooperation, $buildingId)
    {
        // The building the user wants to observe
        $building = Building::find($buildingId)->load('user');

        ObservingToolForUserEvent::dispatch($building, Hoomdossier::user());

        return redirect()->route('cooperation.tool.index');
    }
}
