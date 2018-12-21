<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation\CooperationAdmin;

use App\Models\Cooperation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StepController extends Controller
{
    public function index(Cooperation $cooperation)
    {
        $steps = $cooperation->steps()->get();

        return view('cooperation.admin.cooperation.cooperation-admin.steps.index', compact('steps'));
    }

    public function setActive(Request $request)
    {

    }
}
