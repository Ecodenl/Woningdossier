<?php

namespace App\Http\Controllers\Cooperation\Admin;

use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Admin\Cooperation\UserFormRequest;
use App\Models\Cooperation;
use App\Models\User;
use App\Helpers\SmallMeasuresSettingHelper;
use App\Models\Scan;
use App\Services\UserService;
use App\Traits\Http\CreatesUsers;
use Illuminate\Http\Request;
use App\Models\Role;

class UserController extends Controller
{
    use CreatesUsers;

    public function index(Cooperation $cooperation): View
    {
        // change the relationship to building on merge.
        $users = $cooperation
            ->users()
            ->whereHas('building')
            ->with(['building' => function ($query) {
                $query->with(['buildingStatuses' => function ($query) {
                    $query->with('status');
                }]);
            }])
            ->orderByDesc('created_at')
            ->get();

        return view('cooperation.admin.users.index', compact('users'));
    }

    public function create(Cooperation $cooperation): View
    {
        $userCurrentRole = HoomdossierSession::getRole(true);
        $roles = Role::orderByDesc('level')->get();
        $coaches = $cooperation->getCoaches();

        $allScans = Scan::simpleScans()->get();
        $cooperationScanIds = $cooperation->scans->pluck('id')->toArray();

        $smallMeasuresSettings = [];
        foreach ($allScans as $scan) {
            $smallMeasuresSettings[$scan->short] = [
                'cooperation_enabled' => SmallMeasuresSettingHelper::isEnabledForCooperation($cooperation, $scan),
            ];
        }

        return view('cooperation.admin.users.create', compact(
            'userCurrentRole', 'roles', 'coaches', 'allScans', 'cooperationScanIds', 'smallMeasuresSettings'
        ));
    }

    public function store(UserFormRequest $request, Cooperation $cooperation): RedirectResponse
    {
        $this->createUser($request, $cooperation);

        return redirect()
            ->route('cooperation.admin.users.index')
            ->with('success', __('cooperation/admin/users.store.success'));
    }

    /**
     * Destroy a user.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Cooperation $cooperation, Request $request)
    {
        $userId = $request->get('user_id');

        $user = User::find($userId);

        Gate::authorize('destroy', $user);

        if ($user instanceof User) {
            UserService::deleteUser($user);
        }
    }
}
