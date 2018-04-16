<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Models\Step;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ToolController extends Controller
{
    protected $step;

    public function __construct(Request $request) {
        $slug = str_replace('/tool/', '', $request->getRequestUri());
        $this->step = Step::where('slug', $slug)->first();
    }

    public function index(){
	    $steps = Step::orderBy('order')->get();
	    
    	return view('cooperation.tool.index', compact('steps'));
    }
}
