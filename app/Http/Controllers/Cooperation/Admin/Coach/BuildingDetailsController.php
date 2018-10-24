<?php

namespace App\Http\Controllers\Cooperation\Admin\Coach;

use App\Models\Building;
use App\Models\BuildingNotes;
use App\Models\Cooperation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BuildingDetailsController extends Controller
{
    public function index(Cooperation $cooperation, $buildingId)
    {
        $buildingNotes = BuildingNotes::where('building_id', $buildingId)->orderByDesc('updated_at')->get();
        $building = Building::withTrashed()->find($buildingId);

        return view('cooperation.admin.coach.buildings.details.index', compact('buildingNotes', 'building'));
    }

    public function store(Request $request)
    {
        $note = strip_tags($request->get('note'));
        $buildingId = $request->get('building_id');

        $building = Building::find($buildingId);

        $street = $building->street;
        $number = $building->number;
        $extension = $building->extension;
        $countryCode = $building->country_code;
        $city = $building->city;
        $postalCode = $building->postal_code;
        $bag_addressid = $building->bag_addressid;

        BuildingNotes::create([
            'street' => $street,
            'number' => $number,
            'extension' => $extension,
            'city' => $city,
            'postal_code' => $postalCode,
            'country_code' => $countryCode,
            'bag_addressid' => $bag_addressid,
            'note' => $note,
            'building_id' => $buildingId,
        ]);

        return redirect()->back();
    }
}
