<?php

namespace App\Helpers;

use App\Helpers\KeyFigures\RoofInsulation\Temperature;
use App\Models\Building;
use App\Models\BuildingHeating;
use App\Models\ElementValue;
use App\Models\MeasureApplication;
use App\Models\UserEnergyHabit;
use Carbon\Carbon;

class RoofInsulationCalculator {

	public static function calculateGasSavings(Building $building, ElementValue $element, UserEnergyHabit $energyHabit, BuildingHeating $heating, $surface, $measureAdvice){
		$result = 0;
		$building->getBuildingType();

		$kengetalEnergySaving = Temperature::energySavingFigureRoofInsulation($measureAdvice, $heating);
		self::debug("Kengetal energebesparing = " . $kengetalEnergySaving);

		if (isset($element->calculate_value) && $element->calculate_value < 3){
			$result = min(
				$surface * $kengetalEnergySaving,
				Calculator::maxGasSavings($energyHabit->amount_gas, $building->getBuildingType(), $element->element)
			);
			self::debug($result . " = min(" . $surface . " * " . $kengetalEnergySaving . ", " . Calculator::maxGasSavings($energyHabit->amount_gas, $building->getBuildingType(), $element->element) . ")");
		}
		return $result;
	}

	public static function determineApplicationYear(MeasureApplication $measureApplication, $last){
		self::debug(__METHOD__);

		if ($last + $measureApplication->maintenance_interval <= Carbon::now()->year){
			self::debug("Last replacement is longer than " . $measureApplication->maintenance_interval . " years ago.");
			$year = Carbon::now()->year;
		}
		else {
			$year = $last + $measureApplication->maintenance_interval;
		}

		return $year;
	}

	protected static function debug($line){
		\Log::debug($line);
	}
}