<?php

namespace App\Http\Controllers\Cooperation\Admin;

use App\Helpers\RoleHelper;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SwitchRoleController extends Controller
{

	public function switchRole($cooperation, Request $request, $newRole){

		// check if the targeted role exists
		if (!Role::where('name', $newRole)->exists()){
			\Log::debug("Role '$newRole' does not exist");
			return null;
		}
		/** @var User $user */
		$user = $request->user();
		$role = Role::where('name', $newRole)->first();

		if (!$user || !$user->hasRole($role)){
			\Log::debug("No user or user does not have this role");
			return null;
		}

		$request->session()->put('role_id', $role->id);

		return redirect(RoleHelper::getUrlByRole($role));
	}
}
