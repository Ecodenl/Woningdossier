<?php

namespace App\Http\Controllers\Cooperation\Admin\SuperAdmin;

use App\Helpers\Arr;
use App\Models\Role;
use App\Models\User;
use App\Scopes\CooperationScope;
use function Couchbase\defaultDecoder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function index()
    {
        $roles = Role::whereIn('name', ['coach', 'cooperation-admin', 'coordinator'])->get();

        return view('cooperation.admin.super-admin.users.index', compact('roles'));
    }

    public function filter(Request $request, User $user)
    {
        $roles = Role::whereIn('name', ['coach', 'cooperation-admin', 'coordinator'])->get();

        $userData = $request->input('user');
        $buildingData = $request->input('building');
        $accountData = $request->input('account');

        // prepare the base query to filter on.
        $user = $user->newQuery()->withoutGlobalScope(CooperationScope::class)->with('building');

        if (!is_null($userData['first_name'])) {
            $user->whereLike('first_name', $userData['first_name']);
        }

        if (!is_null($userData['last_name'])) {
            $user->whereLike('last_name', $userData['last_name']);
        }

        if (!is_null($accountData['email'])) {
            $user->whereHas('account', function ($query) use ($accountData) {
                $query->where('email', $accountData['email']);
            });
        }

        if (!is_null($buildingData['city'])) {
            $user->whereHas('building', function ($query) use ($buildingData) {
//               $query->where('city', 'LIKE', '%'.$buildingData['city'].'%');
               $query->whereLike('city', $buildingData['city']);
            });
        }

        // wherelike since the postal code can be typed like: 3248xc 3248XC 3248 xc 3248 XC
        if (!is_null($buildingData['postal_code'])) {
            $user->whereHas('building', function ($query) use ($buildingData) {
               $query->whereLike('postal_code', $buildingData['postal_code']);
            });
        }

        if (!is_null($userData['role_id'])) {
            $user = $user->role($userData['role_id']);
        }

        $users = $user->get();

        return view('cooperation.admin.super-admin.users.show', compact('users', 'userData', 'buildingData', 'accountData', 'roles'));
    }

    public function show()
    {
        return view('cooperation.admin.super-admin.users.show');
    }
}
