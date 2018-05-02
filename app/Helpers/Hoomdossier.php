<?php

namespace App\Helpers;

class Hoomdossier {

	public static function convertDecimal($input){
		return str_replace(',', '.', $input);
	}
}