<?php

namespace App\Http\Controllers\Cooperation\Admin\Coach;

use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;

class CoachController extends Controller
{
    public function index(): RedirectResponse
    {
        // no home page that is relevant, so we redirect them to the building page
        return to_route('cooperation.admin.coach.buildings.index');
    }
}
