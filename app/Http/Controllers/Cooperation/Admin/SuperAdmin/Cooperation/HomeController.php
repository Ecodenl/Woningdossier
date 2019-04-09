<?php

namespace App\Http\Controllers\Cooperation\Admin\SuperAdmin\Cooperation;

use App\Models\Cooperation;
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

        $residentCount = $cooperationToManage->users()->role('resident')->count();
        $coachCount = $cooperationToManage->users()->role('coach')->count();
        $coordinatorCount = $cooperationToManage->users()->role('coordinator')->count();

        return view('cooperation.admin.super-admin.cooperations.home.index', compact(
            'coachCount', 'residentCount', 'coordinatorCount',
            'breadcrumbs', 'cooperationToManage'
        ));
    }
}
