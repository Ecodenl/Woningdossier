<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation\CooperationAdmin;

use App\Http\Controllers\Controller;
use App\Models\Cooperation;

class CooperationAdminController extends Controller
{
    public function index(Cooperation $cooperation)
    {
        $users = $cooperation->users;

        return view('cooperation.admin.cooperation.cooperation-admin.index', compact('users'));
    }
}
