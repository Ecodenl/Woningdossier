<?php

namespace App\Http\Controllers\Cooperation\MyAccount;

use App\Http\Requests\MyAccountSettingsFormRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SettingsController extends Controller
{
    public function index(){
    	$user = \Auth::user();

		return view('cooperation.my-account.settings.index', compact('user'));
    }

    // Update account
    public function store(MyAccountSettingsFormRequest $request){
		$user = \Auth::user();

		$attributes = $request->all();
	    $attributes['phone_number'] = is_null($attributes['phone_number']) ? '' : $attributes['phone_number'];

	    if (!isset($attributes['password']) || empty($attributes['password'])){
	    	unset($attributes['password']);
	    	unset($attributes['password_confirmation']);
	    	unset($attributes['current_password']);
	    }
	    else {
		    $current_password = \Auth::User()->password;
		    if(!\Hash::check($request->get('current_password', ''), $current_password)){
		    	return redirect()->back()->withErrors(['current_password' => __('validation.current_password')]);
		    }
		    $attributes['password'] = \Hash::make($attributes['password']);
	    }

		$user->update($attributes);

	    return redirect()->route('cooperation.my-account.settings.index', ['cooperation' => \App::make('Cooperation')])->with('success', trans('woningdossier.cooperation.my-account.settings.form.store.success'));
    }

    // Delete account
    public function destroy(){

    }

}
