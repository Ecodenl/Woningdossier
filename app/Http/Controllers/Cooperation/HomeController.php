<?php

namespace App\Http\Controllers\Cooperation;

use App\Http\Controllers\Controller;
use App\Models\Cooperation;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Cooperation $cooperation)
    {
        return view('cooperation.home.index');
    }
}
