<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation;

use App\Http\Controllers\Controller;
use App\Models\Cooperation;

class CooperationController extends Controller
{
    public function index(Cooperation $cooperation)
    {
        return view('cooperation.admin.cooperation.cooperation-admin.index');
    }
}
