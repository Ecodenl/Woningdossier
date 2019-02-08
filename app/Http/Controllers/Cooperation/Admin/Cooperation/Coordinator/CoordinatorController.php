<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation\Coordinator;

use App\Http\Controllers\Controller;
use App\Models\Cooperation;

class CoordinatorController extends Controller
{
    public function index(Cooperation $cooperation)
    {

        $users = $cooperation->users;

        return view('cooperation.admin.cooperation.coordinator.index', compact('users'));
    }
}
