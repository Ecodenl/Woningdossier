<?php

namespace App\Http\Controllers\Cooperation;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DisclaimController extends Controller
{
    public function index()
    {
        return view('cooperation.disclaimer.index');
    }
}
