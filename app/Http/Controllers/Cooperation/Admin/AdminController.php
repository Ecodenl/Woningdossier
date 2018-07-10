<?php

namespace App\Http\Controllers\Cooperation\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{

    public function index(){

    	return view('cooperation.admin.admin.index');
    }
}
