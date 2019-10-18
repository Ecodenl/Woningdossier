<?php

namespace App\Http\Controllers\Cooperation\Admin\SuperAdmin\Cooperation;

use App\Http\Controllers\Controller;
use App\Models\Cooperation;

class CooperationAdminController extends Controller
{
    /**
     * Show the cooperation admins of the cooperation that the user is managing.
     *
     * @param Cooperation $currentCooperation
     * @param Cooperation $cooperationToManage
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Cooperation $currentCooperation, Cooperation $cooperationToManage)
    {
        $users = $cooperationToManage->users()->withoutGlobalScopes()->role('cooperation-admin')->get();

        $breadcrumbs = [
            [
                'route' => 'cooperation.admin.super-admin.cooperations.cooperation-to-manage.home.index',
                'url' => route('cooperation.admin.super-admin.cooperations.cooperation-to-manage.home.index', [
                    'cooperation-to-manage' => $cooperationToManage,
                ]),
                'name' => $cooperationToManage->name,
            ],
            [
                'route' => 'cooperation.admin.super-admin.cooperations.cooperation-to-manage.cooperation-admin.index',
                'url' => route('cooperation.admin.super-admin.cooperations.cooperation-to-manage.cooperation-admin.index', [
                    'cooperation-to-manage' => $cooperationToManage,
                ]),
                'name' => __('woningdossier.cooperation.admin.super-admin.cooperations.cooperation-to-manage.side-nav.cooperation-admin'),
            ],
        ];

        return view('cooperation.admin.super-admin.cooperations.cooperation-admin.index', compact('users', 'breadcrumbs', 'cooperationToManage'));
    }
}
