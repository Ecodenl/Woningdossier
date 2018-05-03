<?php

namespace App\Http\Requests;

use App\Helpers\NumberFormatter;

trait DecimalReplacementTrait {

	protected function decimals(array $keys){
		foreach($keys as $key){
			$dec = $this->get($key);

			$dec = NumberFormatter::reverseFormat($dec);
			$this->merge([$key => $dec]);
		}
	}
}