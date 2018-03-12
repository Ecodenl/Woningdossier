<?php

namespace App\Http\Controllers\Cooperation\MyAccount;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SettingsController extends Controller
{
    public function index(){
    	$user = \Auth::user();

		return view('cooperation.my-account.settings.index', compact('user'));
    }

    // Update account
    public function store(Request $request){

    }

    // Delete account
    public function destroy(Request $request){

    }

}
