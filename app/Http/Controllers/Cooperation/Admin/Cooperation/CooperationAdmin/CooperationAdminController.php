<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation\CooperationAdmin;

use App\Models\Cooperation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;

class CooperationAdminController extends Controller
{
    public function index(Cooperation $cooperation)
    {
        $users = $cooperation->users;

        return view('cooperation.admin.cooperation.cooperation-admin.index', compact('users'));
    }
}
