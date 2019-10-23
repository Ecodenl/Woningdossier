<?php

namespace App\Http\Controllers\Cooperation\Tool\GeneralData;

use App\Models\Cooperation;
use App\Models\Step;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BuildingCharacteristicsController extends Controller
{
    public function index(Cooperation $cooperation)
    {
        return view('cooperation.tool.general-data.building-characteristics.index');
    }
}
