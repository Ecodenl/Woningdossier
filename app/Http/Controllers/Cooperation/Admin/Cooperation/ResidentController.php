<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation;

use App\Helpers\RoleHelper;
use App\Http\Controllers\Controller;
use App\Models\Cooperation;

class ResidentController extends Controller
{
    /**
     * Show all the coaches and coordinators.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Cooperation $cooperation)
    {
        $users = $cooperation
            ->users()
            ->with('building', 'roles')
            ->role([RoleHelper::ROLE_RESIDENT])
            ->get();

        return view('cooperation.admin.cooperation.residents.index', compact('users'));
    }
}
