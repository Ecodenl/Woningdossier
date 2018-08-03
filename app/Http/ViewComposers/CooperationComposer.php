<?php

namespace App\Http\ViewComposers;

use App\Models\PrivateMessage;
use Illuminate\View\View;

class CooperationComposer {

	public function create(View $view){
		//\Log::debug(__METHOD__);
		$view->with('cooperation', app()->make('Cooperation'));
		$view->with('cooperationStyle', app()->make('CooperationStyle'));

        $view->with('myUnreadMessages', PrivateMessage::unreadMessages()->get());

	}

}