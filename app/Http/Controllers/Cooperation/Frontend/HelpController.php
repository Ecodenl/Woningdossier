<?php

namespace App\Http\Controllers\Cooperation\Frontend;

use App\Http\Controllers\Controller;

class HelpController extends Controller
{
    public function index()
    {
        return view('cooperation.frontend.help.index');
    }
}
