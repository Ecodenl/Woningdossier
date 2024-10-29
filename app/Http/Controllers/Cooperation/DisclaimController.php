<?php

namespace App\Http\Controllers\Cooperation;

use Illuminate\View\View;
use App\Http\Controllers\Controller;

class DisclaimController extends Controller
{
    public function index(): View
    {
        return view('cooperation.disclaimer.index');
    }
}
