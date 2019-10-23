<?php

namespace App\Http\Controllers\Cooperation;

use App\Http\Controllers\Controller;
use App\Models\Step;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('cooperation.home.index');
    }
}
