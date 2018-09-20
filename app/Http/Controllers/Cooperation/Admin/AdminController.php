<?php

namespace App\Http\Controllers\Cooperation\Admin;

use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    public function index()
    {
        return view('cooperation.admin.admin.index');
    }
}
