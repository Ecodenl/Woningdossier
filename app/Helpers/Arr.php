<?php

namespace App\Helpers;

class Arr {

	/**
	 * The inverse of array_dot.
	 *
	 * @param array $content array of dotted keys to values
	 *
	 * @return array
	 */
	public static function arrayUndot($content)
	{
		$array = array();
		foreach ($content as $key => $value) {
			array_set($array, $key, $value);
		}

		return $array;
	}
}