<?php

namespace App\Http\Controllers\Cooperation\Admin;

use App\Helpers\RoleHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{

    public function index()
    {
        $user = \Auth::user();

        // if the user only has one role we will redirect him to the right url
        if ($user->getRoleNames()->count() == 1) {

            $roleName = $user->getRoleNames()->first();

            if ($roleName == "cooperation-admin") {
                return redirect(url(RoleHelper::getUrlByRoleName($roleName)));
            }
            else if ($roleName == "coordinator") {
                return redirect(url(RoleHelper::getUrlByRoleName($roleName)));
            }
            else if ($roleName == "coach") {
                return redirect(url(RoleHelper::getUrlByRoleName($roleName)));
            }
        }
    	return view('cooperation.admin.choose-roles', compact('user'));
    }
}
