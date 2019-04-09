<?php

namespace App\Http\Controllers\Cooperation\Admin\SuperAdmin\Cooperation;

use App\Models\Cooperation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CoordinatorController extends Controller
{
    /**
     * Show the coordinators of the cooperation that the user is managing
     *
     * @param Cooperation $currentCooperation
     * @param Cooperation $cooperationToManage
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Cooperation $currentCooperation, Cooperation $cooperationToManage)
    {
        $users = $cooperationToManage->users()->role('coordinator')->get();

        $breadcrumbs = [
            [
                'route' => 'cooperation.admin.super-admin.cooperations.cooperation-to-manage.home.index',
                'url' => route('cooperation.admin.super-admin.cooperations.cooperation-to-manage.home.index', [$currentCooperation, $cooperationToManage]),
                'name' => $cooperationToManage->name,
            ],
            [
                'route' => 'cooperation.admin.super-admin.cooperations.cooperation-to-manage.coordinator.index',
                'url' => route('cooperation.admin.super-admin.cooperations.cooperation-to-manage.coordinator.index', [$currentCooperation, $cooperationToManage]),
                'name' => __('woningdossier.cooperation.admin.super-admin.cooperations.cooperation-to-manage.side-nav.coordinator')
            ]
        ];

        return view('cooperation.admin.super-admin.cooperations.coordinator.index', compact('users', 'breadcrumbs', 'cooperationToManage'));
    }
}

