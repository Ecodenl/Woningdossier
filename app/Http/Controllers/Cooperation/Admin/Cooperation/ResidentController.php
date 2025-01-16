<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation;

use Illuminate\View\View;
use App\Helpers\RoleHelper;
use App\Http\Controllers\Controller;
use App\Models\Cooperation;

class ResidentController extends Controller
{
    /**
     * Show all the coaches and coordinators.
     */
    public function index(Cooperation $cooperation): View
    {
        $users = $cooperation
            ->users()
            ->with('building', 'roles')
            ->role([RoleHelper::ROLE_RESIDENT])
            ->get();

        return view('cooperation.admin.cooperation.residents.index', compact('users'));
    }
}
