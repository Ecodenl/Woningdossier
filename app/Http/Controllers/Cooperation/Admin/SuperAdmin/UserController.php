<?php

namespace App\Http\Controllers\Cooperation\Admin\SuperAdmin;

use Illuminate\View\View;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Scopes\CooperationScope;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(): View
    {
        $roles = Role::whereIn('name', ['coach', 'cooperation-admin', 'coordinator'])->get();

        return view('cooperation.admin.super-admin.users.index', compact('roles'));
    }

    public function filter(Request $request, User $user): View
    {
        $roles = Role::whereIn('name', ['coach', 'cooperation-admin', 'coordinator'])->get();

        $userData = $request->input('user');
        $buildingData = $request->input('building');
        $accountData = $request->input('account');

        // prepare the base query to filter on.
        $user = $user->newQuery()->withoutGlobalScope(CooperationScope::class)
            ->whereHas('cooperation')
            ->withWhereHas('building');

        if (! is_null($userData['first_name'])) {
            $user->where('first_name', 'LIKE', "%{$userData['first_name']}%");
        }

        if (! is_null($userData['last_name'])) {
            $user->where('last_name', 'LIKE', "%{$userData['last_name']}%");
        }

        if (! is_null($accountData['email'])) {
            $user->whereHas('account', function ($query) use ($accountData) {
                $query->where('email', $accountData['email']);
            });
        }

        if (! is_null($buildingData['city'])) {
            $user->whereHas('building', function ($query) use ($buildingData) {
                $query->where('city', 'LIKE', "%{$buildingData['city']}%");
            });
        }

        // where like since the postal code can be typed like: 1234xc 1234XC 1234 xc 1234 XC
        if (! is_null($buildingData['postal_code'])) {
            $user->whereHas('building', function ($query) use ($buildingData) {
                $query->where('postal_code', 'LIKE', "%{$buildingData['postal_code']}%");
            });
        }

        if (! is_null($userData['role_id'])) {
            $user = $user->role($userData['role_id']);
        }

        $users = $user->get();

        return view('cooperation.admin.super-admin.users.show', compact('users', 'userData', 'buildingData', 'accountData', 'roles'));
    }

    public function show(): View
    {
        return view('cooperation.admin.super-admin.users.show');
    }
}
