<?php

namespace App\Http\Controllers\Cooperation\Admin\SuperAdmin;

use App\Helpers\Arr;
use Illuminate\View\View;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Scopes\CooperationScope;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        return $this->filter($request);
    }

    public function filter(Request $request): View
    {
        $roles = Role::whereIn('name', ['coach', 'cooperation-admin', 'coordinator'])->get();

        $userData = $request->input('user', []);
        $buildingData = $request->input('building', []);
        $accountData = $request->input('account', []);

        if (Arr::isWholeArrayEmpty($userData) && Arr::isWholeArrayEmpty($buildingData) && Arr::isWholeArrayEmpty($accountData)) {
            // Searching should at least contain one value to ensure we don't crash the page.
            $errors = [];
            // If a request was done from the form, then there will be array keys with null values in the
            // array. This is how we know the error should be shown.
            if (! empty($userData)) {
                $errors[] = __('cooperation/admin/super-admin/users.index.search.empty-warning');
            }

            $users = collect();
            return view(
                'cooperation.admin.super-admin.users.index',
                compact('users', 'userData', 'buildingData', 'accountData', 'roles')
            )->withErrors($errors);
        }

        // prepare the base query to filter on.
        $user = User::withoutGlobalScope(CooperationScope::class)
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
            $user = $user->role((int) $userData['role_id']);
        }

        $users = $user->get();

        return view(
            'cooperation.admin.super-admin.users.index',
            compact('users', 'userData', 'buildingData', 'accountData', 'roles')
        );
    }
}
