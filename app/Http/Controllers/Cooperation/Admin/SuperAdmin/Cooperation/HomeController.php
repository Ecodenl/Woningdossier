<?php

namespace App\Http\Controllers\Cooperation\Admin\SuperAdmin\Cooperation;

use App\Models\Cooperation;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index(Cooperation $currentCooperation, Cooperation $cooperationToManage)
    {
        $breadcrumbs = [
            [
                'route' => 'cooperation.admin.super-admin.cooperations.cooperation-to-manage.home.index',
                'url' => route('cooperation.admin.super-admin.cooperations.cooperation-to-manage.home.index', [
                    'cooperation-to-manage' => $cooperationToManage
                ]),
                'name' => $cooperationToManage->name,
            ]
        ];

        $coachRole = Role::where('name', 'coach')->first();
        $residentRole = Role::where('name', 'resident')->first();
        $coordinatorRole = Role::where('name', 'coordinator')->first();


        $coachCount = $cooperationToManage->getUsersWithRole($coachRole)->count();

        $residentCount = $cooperationToManage->getUsersWithRole($residentRole)->count();

        $coordinatorCount = $cooperationToManage->getUsersWithRole($coordinatorRole)->count();

        return view('cooperation.admin.super-admin.cooperations.home.index', compact(
            'coachCount', 'residentCount', 'coordinatorCount',
            'breadcrumbs', 'cooperationToManage'
        ));
    }
}
