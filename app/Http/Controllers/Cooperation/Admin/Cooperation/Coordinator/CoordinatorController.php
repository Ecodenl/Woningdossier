<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation\Coordinator;

use App\Http\Controllers\Controller;
use App\Models\Cooperation;

class CoordinatorController extends Controller
{
    public function index(Cooperation $cooperation)
    {
        return redirect(route('cooperation.admin.cooperation.users.index'));
    }
}
