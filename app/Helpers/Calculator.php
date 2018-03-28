<?php

namespace App\Helpers;

use App\Models\Building;
use App\Models\BuildingType;
use App\Models\BuildingTypeElementMaxSaving;
use App\Models\Element;
use App\Models\ElementValue;

class Calculator {

	public static function calculateGasSavings(Building $building, ElementValue $element, $surface, $gasUsage){
		$result = 0;
		$building->getBuildingType();
		if (isset($element->calculate_value) && $element->calculate_value < 3){
			$result = min(
				$surface * Kengetallen::ENERGY_SAVING_WALL_INSULATION,
				self::maxGasSavings($gasUsage, $building->getBuildingType(), $element->element)
			);
			self::debug($result . " = min(" . $surface . " * " . Kengetallen::ENERGY_SAVING_WALL_INSULATION . ", " . self::maxGasSavings($gasUsage, $building->getBuildingType(), $element->element) . ")");
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
	public static function maxGasSavings($usage, BuildingType $buildingType, Element $element){
		$saving = 0;
		$maxSaving = BuildingTypeElementMaxSaving::where('building_type_id', $buildingType->id)
			->where('element_id', $element->id)
			->first();
		if ($maxSaving instanceof BuildingTypeElementMaxSaving) {
			$saving = $maxSaving->max_saving;
		}
		self::debug("Max saving for building_type " . $buildingType->id . " + element " . $element->id . " = " . $saving);
		$result = $usage * $saving;
		self::debug($result . " = " . $usage . " * " . $saving);
		return $result;
	}

	protected static function debug($line){
		\Log::debug($line);
	}
}