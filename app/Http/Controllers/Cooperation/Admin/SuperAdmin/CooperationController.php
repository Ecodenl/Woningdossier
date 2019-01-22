<?php

namespace App\Http\Controllers\Cooperation\Admin\SuperAdmin;

use App\Models\Cooperation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CooperationController extends Controller
{
    public function index()
    {
        $cooperations = Cooperation::all();

        return view('cooperation.admin.super-admin.cooperations.index', compact('cooperations'));
    }
}
