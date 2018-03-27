<?php

namespace App\Helpers;

use Ramsey\Uuid\Uuid;

class Str {

	/**
	 * Uuid generation wrapping. Laravel < 5.6 uses Ramsey\Uuid. From 5.6 it is
	 * put in the \Illuminate\Support\Str helper.
	 * @return string
	 */
	public static function uuid(){
		$laravel = app();
		$laravel::VERSION;
		if (version_compare($laravel::VERSION, '5.6.0') < 0){
			// use
			return (string) Uuid::uuid4();
		}
		else {
			return (string) \Illuminate\Support\Str::uuid();
		}
	}
}