<?php

namespace App\Http\Controllers\Cooperation\Frontend\Tool\QuickScan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MyPlanController extends Controller
{
    public function index()
    {
        return view('cooperation.frontend.tool.quick-scan.my-plan.index');
    }
}
