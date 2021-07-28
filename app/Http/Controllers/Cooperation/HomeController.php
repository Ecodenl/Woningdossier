<?php

namespace App\Http\Controllers\Cooperation;

use App\Http\Controllers\Controller;
use App\Models\Cooperation;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @param  \App\Models\Cooperation  $cooperation
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Cooperation $cooperation)
    {
        return view('cooperation.home.index');
    }
}
