<?php

namespace App\Helpers\KeyFigures\FloorInsulation;

class Temperature {

	const FLOOR_INSULATION_FLOOR = 'Vloerisolatie';
	const FLOOR_INSULATION_BOTTOM = 'Bodemisolatie';
	const FLOOR_INSULATION_RESEARCH = 'Nader onderzoek nodig';

	protected static $calculationValues = [
		self::FLOOR_INSULATION_FLOOR => 4.04, // D27
		self::FLOOR_INSULATION_BOTTOM => 3.51, // D28
		self::FLOOR_INSULATION_RESEARCH => 3.51, // D29 = D28
	];

	/**
	 * kengetal energiebesparing
	 * @param string $measure Use WALL_INSULATION_* consts
	 * @param $avgHouseTemp
	 * @return null|string Null on failure
	 */
	public static function energySavingFigureFloorInsulation($measure){
		if (!array_key_exists($measure, self::$calculationValues)) return null;

		return number_format(self::$calculationValues[$measure], 2);
	}
}