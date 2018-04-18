<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Http\Controllers\Controller;
use App\Models\Step;
use App\Models\UserActionPlanAdvice;
use Illuminate\Http\Request;

class MyPlanController extends Controller
{

	public function index(){
		$user = \Auth::user();
		$advices = UserActionPlanAdvice::getCategorizedActionPlan($user);
		//$advices = $user->actionPlanAdvices()->orderBy('year', 'asc')->get();
		$steps = Step::orderBy('order')->get();

		return view('cooperation.tool.my-plan.index', compact(
			'advices', 'steps'
		));
	}

	public function store(Request $request){

	}
}
