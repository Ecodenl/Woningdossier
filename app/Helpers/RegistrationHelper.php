<?php

namespace App\Helpers;

class RegistrationHelper {

	public static function generateConfirmToken($length = 64){
		return str_random($length);
	}
}