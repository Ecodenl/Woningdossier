<?php

namespace App\Http\Controllers\Cooperation\Admin;

use App\Helpers\HoomdossierSession;
use App\Helpers\RoleHelper;
use App\Http\Controllers\Controller;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Role;

class AdminController extends Controller
{
    public function stopSession(Cooperation $cooperation)
    {
        return redirect()->route('cooperation.admin.switch-role', HoomdossierSession::currentRole());
    }

    public function index()
    {
        $user = \Auth::account()->user();

        // if the user only has one role we will redirect him to the right url
        if (1 == $user->getRoleNames()->count()) {
            $roleName = $user->getRoleNames()->first();

            return redirect()->route('cooperation.admin.switch-role', $roleName);
        }

        return view('cooperation.admin.choose-roles', compact('user'));
    }
}
