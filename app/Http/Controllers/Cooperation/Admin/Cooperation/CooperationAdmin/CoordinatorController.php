<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation\CooperationAdmin;

use App\Helpers\Str;
use App\Http\Requests\Cooperation\Admin\Cooperation\CooperationAdmin\CoordinatorRequest;
use App\Mail\UserCreatedEmail;
use App\Models\Cooperation;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;

class CoordinatorController extends Controller
{
    public function index(Cooperation $cooperation)
    {
        $users = $cooperation->getCoordinators()->get();

        return view('cooperation.admin.cooperation.cooperation-admin.coordinator.index', compact('users'));
    }

    public function create()
    {
        return view('cooperation.admin.cooperation.cooperation-admin.coordinator.create');
    }

    public function store(Cooperation $cooperation, CoordinatorRequest $request)
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

        $user->assignRole('coordinator');

        // send a mail to the user
        \Mail::to($email)->sendNow(new UserCreatedEmail($cooperation));

        return redirect()
            ->route('cooperation.admin.cooperation.cooperation-admin.coordinator.index')
            ->with('success', __('woningdossier.cooperation.admin.cooperation.cooperation-admin.coordinator.store.success'));
    }
}