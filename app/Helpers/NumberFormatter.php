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
		if (is_null($number)){
			$number = 0;
		}

		\Log::debug("anders kanker je ff op. " . $locale . " | " . $number);

		try {
			$f = number_format(
				$number,
				$decimals,
				self::$localeSeparators[ $locale ]['decimal'],
				self::$localeSeparators[ $locale ]['thousands']
			);
			return $f;
		}
		catch(\Exception $e){
			\Log::debug("Ik ben een achterlijke grafkankertyfusmongool: " . $number . ", " . $decimals . ", " . self::$localeSeparators[ $locale ]['decimal'] . ", " . self::$localeSeparators[ $locale ]['thousands']);
		}
	}

	public static function reverseFormat($number){
		$locale = app()->getLocale();
		if (is_null($number)){
			$number = 0;
		}

		$number = str_replace(
			[ self::$localeSeparators[$locale]['thousands'] , " ", ],
			[ '', '' ],
			$number
		);
		return str_replace(self::$localeSeparators[$locale]['decimal'], '.', $number);
	}
}