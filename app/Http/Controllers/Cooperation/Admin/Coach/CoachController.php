<?php

namespace App\Http\Controllers\Cooperation\Admin\Coach;

use App\Models\Cooperation;
use App\Http\Controllers\Controller;

class CoachController extends Controller
{
    public function index(Cooperation $cooperation)
    {
        return redirect()->route('cooperation.admin.coach.buildings.index');
    }
}
