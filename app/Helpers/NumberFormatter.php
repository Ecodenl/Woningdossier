<?php

namespace App\Helpers;

class NumberFormatter {

	/**
	 * Separators for viewing (so, mostly used in the views or data for the view)
	 * @var array
	 */
	protected static $formatLocaleSeparators = [
		'nl' => [
			'decimal' => ',',
			'thousands' => '.',
		],
		'en' => [
			'decimal' => '.',
			'thousands' => ',',
		],
	];

	/**
	 * For reversing from view to controller. We could define just the differences
	 * from the $formatLocaleSeparators, but that might be more confusing than
	 * just duplicating.
	 *
	 * @var array
	 */
	protected static $reverseLocaleSeparators = [
		'nl' => [
			'decimal' => ',',
			'thousands' => '', // different! If people fill in a dot, treat it like a comma (and so: a decimal)
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
		return number_format(
				$number,
				$decimals,
				self::$formatLocaleSeparators[ $locale ]['decimal'],
				self::$formatLocaleSeparators[ $locale ]['thousands']
		);
	}

	public static function reverseFormat($number){
		$locale = app()->getLocale();
		if (is_null($number)){
			$number = 0;
		}

		$number = str_replace(
			[ self::$reverseLocaleSeparators[$locale]['thousands'] , " ", ],
			[ '', '' ],
			$number
		);
		return str_replace(self::$reverseLocaleSeparators[$locale]['decimal'], '.', $number);
	}
}