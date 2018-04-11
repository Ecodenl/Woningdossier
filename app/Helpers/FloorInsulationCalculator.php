<?php

namespace App\Helpers;

use App\Helpers\KeyFigures\FloorInsulation\Temperature;
use App\Models\Building;
use App\Models\ElementValue;
use App\Models\UserEnergyHabit;

class FloorInsulationCalculator {

	public static function calculateGasSavings(Building $building, ElementValue $element, UserEnergyHabit $energyHabit, $surface, $measureAdvice){
		$result = 0;
		$building->getBuildingType();

		$kengetalEnergySaving = Temperature::energySavingFigureFloorInsulation($measureAdvice);
		self::debug("Kengetal energebesparing = " . $kengetalEnergySaving);

		if (isset($element->calculate_value) && $element->calculate_value < 3){
			$result = min(
				$surface * $kengetalEnergySaving,
				Calculator::maxGasSavings($energyHabit->amount_gas, $building->getBuildingType(), $element->element)
			);
			self::debug($result . " = min(" . $surface . " * " . $kengetalEnergySaving . ", " . Calculator::maxGasSavings($energyHabit->amount_gas, $building->getBuildingType(), $element->element) . ")");
		}
		else {
			self::debug("No gas savings..");
		}
		return $result;
	}

	protected static function debug($line){
		\Log::debug($line);
	}
}