<?php

namespace App\Http\Controllers\Cooperation\Admin\Coach;

use App\Http\Controllers\Controller;
use App\Models\Cooperation;

class CoachController extends Controller
{
    public function index()
    {
        // no home page that is relevant, so we redirect them to the building page
        return redirect()->route('cooperation.admin.coach.buildings.index');
    }
}
