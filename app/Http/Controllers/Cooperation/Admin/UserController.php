<?php

namespace App\Http\Controllers\Cooperation\Admin;

use App\Helpers\Hoomdossier;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Admin\Cooperation\UserFormRequest;
use App\Models\Cooperation;
use App\Models\User;
use App\Services\UserService;
use App\Traits\Http\CreatesUsers;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    use CreatesUsers;

    public function index(Cooperation $cooperation)
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

    public function create(Cooperation $cooperation)
    {
        $possibleRoles = Role::orderByDesc('level')->get();
        $roles = [];
        foreach ($possibleRoles as $possibleRole) {
            if (Hoomdossier::account()->can('assign-role', $possibleRole)) {
                $roles[] = $possibleRole;
            }
        }
        $roles = collect($roles);
        $coaches = $cooperation->getCoaches()->get();

        return view('cooperation.admin.users.create', compact('roles', 'coaches'));
    }

    public function store(UserFormRequest $request, Cooperation $cooperation)
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

        $this->authorize('destroy', $user);

        if ($user instanceof User) {
            UserService::deleteUser($user);
        }
    }
}