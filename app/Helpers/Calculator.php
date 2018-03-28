<?php

namespace App\Helpers;

use App\Models\Building;
use App\Models\BuildingType;
use App\Models\ElementValue;

class Calculator {

	public static function calculateGasSavings(Building $building, ElementValue $element, $surface){
		$result = 0;
		$building->getBuildingType();
		if (isset($element->calculate_value) && $element->calculate_value < 3){
			$result = min(
				$surface * Kengetallen::ENERGY_SAVING_WALL_INSULATION,
				self::maxGasSavings($building->getBuildingType())
			);
			self::debug($result . " = min(" . $surface . " * " . Kengetallen::ENERGY_SAVING_WALL_INSULATION . ", " . self::maxGasSavings($building->getBuildingType()) . ")");
		}
		return $result;
	}

	public static function calculateCo2Savings($gasSavings){
		$result = $gasSavings * Kengetallen::CO2_SAVING_GAS;
		self::debug($result . " = " . $gasSavings . " * " . Kengetallen::CO2_SAVING_GAS);
		return $result;
	}

	public static function calculateMoneySavings($gasSavings){
		$result = $gasSavings * Kengetallen::EURO_SAVINGS_GAS;
		self::debug($result . " = " . $gasSavings . " * " . Kengetallen::EURO_SAVINGS_GAS);
		return $result;
	}

	// in m3 per year
	public static function maxGasSavings(BuildingType $buildingType){

		return 220;
	}

	protected static function debug($line){
		\Log::debug($line);
	}
}