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

        $coachCount = $cooperationToManage->users()->withoutGlobalScopes()->role('coach')->count();

        $residentCount = $cooperationToManage->users()->withoutGlobalScopes()->role('resident')->count();

        $coordinatorCount = $cooperationToManage->users()->withoutGlobalScopes()->role('coordinator')->count();

        return view('cooperation.admin.super-admin.cooperations.home.index', compact(
            'coachCount', 'residentCount', 'coordinatorCount',
            'breadcrumbs', 'cooperationToManage'
        ));
    }
}
