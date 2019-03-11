<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation;

use App\Models\Cooperation;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CoachController extends Controller
{
    /**
     * We only want to show the coaches. nothing special in this controller.
     *
     * @param Role $cooperation
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Cooperation $cooperation)
    {
        $users = $cooperation
                ->users()
                ->role('coach')
                ->where('id', '!=', \Auth::id())
                ->get();

        $roles = Role::all();

        return view('cooperation.admin.cooperation.coaches.index', compact('roles', 'users'));
    }
}
