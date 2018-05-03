<?php

namespace App\Http\Controllers\Cooperation;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HelpController extends Controller
{
    public function index()
    {
        return view('cooperation.help.index');
    }
}
