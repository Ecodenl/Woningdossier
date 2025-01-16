<?php

namespace App\Http\Controllers\Cooperation\Frontend;

use Illuminate\View\View;
use App\Http\Controllers\Controller;

class HelpController extends Controller
{
    public function index(): View
    {
        return view('cooperation.frontend.help.index');
    }
}
