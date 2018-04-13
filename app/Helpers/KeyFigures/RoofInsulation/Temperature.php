<?php

namespace App\Helpers\KeyFigures\RoofInsulation;

use App\Models\BuildingHeating;

class Temperature {

	const ROOF_INSULATION_PITCHED_INSIDE = 'pitched, from inside';
	const ROOF_INSULATION_PITCHED_REPLACE_TILES = 'pitched, replace tiles';
	const ROOF_INSULATION_FLAT_ON_CURRENT = 'flat, current';
	const ROOF_INSULATION_FLAT_REPLACE = 'flat, replace';

	protected static $calculationValues = [
		self::ROOF_INSULATION_PITCHED_INSIDE => [
			2 => 10.24,
			3 => 7.41,
			4 => 5.01,
		],
		self::ROOF_INSULATION_PITCHED_REPLACE_TILES => [
			2 => 11.13,
			3 => 8.05,
			4 => 5.44,
		],
		self::ROOF_INSULATION_FLAT_ON_CURRENT => [
			2 => 8.64,
			3 => 6.25,
			4 => 4.23,
		],
		self::ROOF_INSULATION_FLAT_REPLACE => [
			2 => 11.44,
			3 => 8.27,
			4 => 5.59,
		]
	];

	/**
	 * kengetal energiebesparing
	 * @param string $measure Use ROOF_INSULATION_* consts
	 * @param $avgHouseTemp
	 * @return null|string Null on failure
	 */
	public static function energySavingFigureRoofInsulation($measure, BuildingHeating $heating){
		if (!array_key_exists($measure, self::$calculationValues)) return null;
		if (!array_key_exists($heating->calculate_value, self::$calculationValues[$measure])) return 0;

		return number_format(self::$calculationValues[$measure][$heating->calculate_value], 2);
	}
}