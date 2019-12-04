<?php

namespace App\Http\Controllers\Cooperation\Admin\SuperAdmin\Cooperation;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Cooperation;
use App\Models\Role;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Show the coordinators of the cooperation that the user is managing.
     *
     * @param  Cooperation  $currentCooperation
     * @param  Cooperation  $cooperationToManage
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(
        Cooperation $currentCooperation,
        Cooperation $cooperationToManage
    ) {
        $users = $cooperationToManage->users()->withoutGlobalScopes()->get();

        $breadcrumbs = [
            [
                'route' => 'cooperation.admin.super-admin.cooperations.cooperation-to-manage.home.index',
                'url'   => route('cooperation.admin.super-admin.cooperations.cooperation-to-manage.home.index',
                    [$currentCooperation, $cooperationToManage]),
                'name'  => $cooperationToManage->name,
            ],
            [
                'route' => 'cooperation.admin.super-admin.cooperations.cooperation-to-manage.users.index',
                'url'   => route('cooperation.admin.super-admin.cooperations.cooperation-to-manage.coordinator.index',
                    [$currentCooperation, $cooperationToManage]),
                'name'  => __('woningdossier.cooperation.admin.super-admin.cooperations.cooperation-to-manage.side-nav.users'),
            ],
        ];

        return view('cooperation.admin.super-admin.cooperations.users.index',
            compact('users', 'breadcrumbs', 'cooperationToManage'));
    }

    public function show(
        Cooperation $currentCooperation,
        Cooperation $cooperationToManage,
        $userId
    ) {
        $user = User::withoutGlobalScopes()->findOrFail($userId);
        $roles = Role::where('name', '!=', 'superuser')
                     ->where('name', '!=', 'super-admin')->get();

        return view('cooperation.admin.super-admin.cooperations.users.show',
            compact('user', 'breadcrumbs', 'cooperationToManage', 'roles'));
    }

    public function confirm(Cooperation $currentCooperation, Cooperation $cooperationToManage, $accountId)
    {
        $account = Account::findOrFail($accountId);

        $account->confirm_token = null;
        $account->save();

        return redirect()->back()->with('success', __('my-account.settings.reset-file.success'));
    }
}
