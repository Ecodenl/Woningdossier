<?php

namespace App\Http\Controllers\Cooperation\Admin;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    public function index()
    {
        $user = \Auth::user();

        // if the user only has one role we will redirect him to the right url
        if (1 == $user->getRoleNames()->count()) {
            $roleName = $user->getRoleNames()->first();

            return redirect()->route('cooperation.admin.switch-role', $roleName);
        }

        return view('cooperation.admin.choose-roles', compact('user'));
    }
}
