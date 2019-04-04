<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation\CooperationAdmin;

use App\Http\Controllers\Controller;
use App\Models\Cooperation;

class CooperationAdminController extends Controller
{
    public function index(Cooperation $cooperation)
    {
        return redirect(route('cooperation.admin.cooperation.users.index'));
    }
}
