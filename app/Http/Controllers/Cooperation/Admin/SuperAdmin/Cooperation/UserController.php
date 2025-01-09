<?php

namespace App\Http\Controllers\Cooperation\Admin\SuperAdmin\Cooperation;

use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Admin\Cooperation\UserFormRequest;
use App\Models\Account;
use App\Models\Cooperation;
use App\Models\Role;
use App\Models\User;
use App\Traits\Http\CreatesUsers;

class UserController extends Controller
{
    use CreatesUsers;

    /**
     * Show the coordinators of the cooperation that the user is managing.
     */
    public function index(Cooperation $cooperation, Cooperation $cooperationToManage): View
    {
        $users = $cooperationToManage->users()->withoutGlobalScopes()->get();

        $breadcrumbs = [
            [
                'route' => 'cooperation.admin.super-admin.cooperations.cooperation-to-manage.home.index',
                'url' => route('cooperation.admin.super-admin.cooperations.cooperation-to-manage.home.index', compact('cooperation', 'cooperationToManage')),
                'name' => $cooperationToManage->name,
            ],
            [
                'route' => 'cooperation.admin.super-admin.cooperations.cooperation-to-manage.users.index',
                'url' => route('cooperation.admin.super-admin.cooperations.cooperation-to-manage.users.index', compact('cooperation', 'cooperationToManage')),
                'name' => __('woningdossier.cooperation.admin.super-admin.cooperations.cooperation-to-manage.side-nav.users'),
            ],
        ];

        return view(
            'cooperation.admin.super-admin.cooperations.users.index',
            compact('users', 'breadcrumbs', 'cooperationToManage')
        );
    }

    public function create(Cooperation $cooperation, Cooperation $cooperationToManage): View
    {
        $userCurrentRole = HoomdossierSession::getRole(true);
        $roles = Role::orderByDesc('level')->get();
        $coaches = $cooperationToManage->getCoaches();

        return view('cooperation.admin.users.create', compact('userCurrentRole', 'roles', 'coaches', 'cooperationToManage'));
    }

    public function store(UserFormRequest $request, Cooperation $cooperation, Cooperation $cooperationToManage): RedirectResponse
    {
        $this->createUser($request, $cooperationToManage);

        return redirect()
            ->route(
                'cooperation.admin.super-admin.cooperations.cooperation-to-manage.users.index',
                compact('cooperation', 'cooperationToManage')
            )
            ->with('success', __('cooperation/admin/users.store.success'));
    }

    public function show(Cooperation $currentCooperation, Cooperation $cooperationToManage, $userId): View
    {
        $user = User::withoutGlobalScopes()->findOrFail($userId);
        $building = $user->building;
        $roles = Role::orderByDesc('level')->get();
        $userCurrentRole = HoomdossierSession::getRole(true);

        return view(
            'cooperation.admin.super-admin.cooperations.users.show',
            compact('user', 'cooperationToManage', 'roles', 'userCurrentRole', 'building')
        );
    }

    public function confirm(Cooperation $currentCooperation, Cooperation $cooperationToManage, $accountId): RedirectResponse
    {
        $account = Account::findOrFail($accountId);
        $account->markEmailAsVerified();

        return redirect()->back()->with('success', __('Account bevestigd'));
    }
}
