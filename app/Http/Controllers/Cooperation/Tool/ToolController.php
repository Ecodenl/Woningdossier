<?php

namespace App\Http\Controllers\Cooperation\Tool;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ToolController extends Controller
{
    public function index(){

    	return view('cooperation.tool.index');
    }
}
