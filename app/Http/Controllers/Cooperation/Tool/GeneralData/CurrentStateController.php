<?php

namespace App\Http\Controllers\Cooperation\Tool\GeneralData;

use App\Helpers\HoomdossierSession;
use App\Models\Cooperation;
use App\Models\Element;
use App\Models\Service;
use App\Models\UserInterest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CurrentStateController extends Controller
{
    public function index(Cooperation $cooperation)
    {
        $building = HoomdossierSession::getBuilding(true);
        $buildingOwner = $building->user;
        $userInterestsForMe = UserInterest::forMe()->get();

        $services = Service::orderBy('order')->get();
        $elements = Element::whereIn('short', [
            'sleeping-rooms-windows', 'living-rooms-windows',
            'wall-insulation', 'floor-insulation', 'roof-insulation',
        ])->orderBy('order')->get();

        return view('cooperation.tool.general-data.current-state.index', compact(
            'building', 'buildingOwner', 'elements', 'services', 'userInterestsForMe'
        ));
    }
}
