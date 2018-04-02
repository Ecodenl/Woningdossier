<?php

namespace App\Helpers;

class NumberFormatter {

	protected static $localeSeparators = [
		'nl' => [
			'decimal' => ',',
			'thousands' => '.',
		],
		'en' => [
			'decimal' => '.',
			'thousands' => ',',
		],
	];

	public static function format($number, $decimals = 0){
		$locale = app()->getLocale();
		return number_format(
			$number,
			$decimals,
			self::$localeSeparators[$locale]['decimal'],
			self::$localeSeparators[$locale]['thousands']
		);
	}
}