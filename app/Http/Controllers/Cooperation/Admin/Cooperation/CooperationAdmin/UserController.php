<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation\CooperationAdmin;

use App\Helpers\Str;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Admin\Cooperation\CooperationAdmin\UsersRequest;
use App\Mail\UserCreatedEmail;
use App\Models\Cooperation;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Cooperation $cooperation)
    {
        $users = $cooperation->getCoordinators()->get();

        return view('cooperation.admin.cooperation.cooperation-admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::where('name', 'coach')->orWhere('name', 'resident')->orWhere('name', 'coordinator')->get();

        return view('cooperation.admin.cooperation.cooperation-admin.users.create', compact('roles'));
    }

    public function store(Cooperation $cooperation, UsersRequest $request)
    {
        $firstName = $request->get('first_name', '');
        $lastName = $request->get('last_name', '');
        $email = $request->get('email', '');
        $password = $request->get('password', Str::randomPassword());

        // so we dont need to attach it manualy
        $user = $cooperation->users()->create(
            [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'password' => bcrypt($password),
            ]
        );

        $roleIds = $request->get('roles', '');

        $roles = [];
        foreach ($roleIds as $roleId) {
            $role = Role::find($roleId);
            array_push($roles, $role->name);
        }

        // assign the roles to the user
        $user->assignRole($roles);

        // send a mail to the user
        \Mail::to($email)->sendNow(new UserCreatedEmail($cooperation));

        return redirect()
            ->route('cooperation.admin.cooperation.cooperation-admin.users.index')
            ->with('success', __('woningdossier.cooperation.admin.cooperation.cooperation-admin.users.store.success'));
    }
}
