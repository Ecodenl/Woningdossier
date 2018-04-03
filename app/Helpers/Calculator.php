<?php

namespace App\Helpers;

use App\Helpers\Calculation\RoomTemperatureCalculator;
use App\Helpers\KeyFigures\WallInsulation\Temperature;
use App\Models\Building;
use App\Models\BuildingType;
use App\Models\BuildingTypeElementMaxSaving;
use App\Models\Element;
use App\Models\ElementValue;
use App\Models\MeasureApplication;
use App\Models\UserEnergyHabit;

class Calculator {

	public static function calculateGasSavings(Building $building, ElementValue $element, UserEnergyHabit $energyHabit, $surface, $measureAdvice){
		$result = 0;
		$building->getBuildingType();

		$roomTempCalculator = new RoomTemperatureCalculator($energyHabit);
		$averageHouseTemperature = $roomTempCalculator->getAverageHouseTemperature();
		self::debug("Average house temperature = " . $averageHouseTemperature);
		$kengetalEnergySaving = Temperature::energySavingFigureWallInsulation($measureAdvice, $averageHouseTemperature);
		self::debug("Kengetal energebesparing = " . $kengetalEnergySaving);

		if (isset($element->calculate_value) && $element->calculate_value < 3){
			$result = min(
				$surface * $kengetalEnergySaving,
				self::maxGasSavings($energyHabit->amount_gas, $building->getBuildingType(), $element->element)
			);
			self::debug($result . " = min(" . $surface . " * " . $kengetalEnergySaving . ", " . self::maxGasSavings($energyHabit->amount_gas, $building->getBuildingType(), $element->element) . ")");
		}
		return $result;
	}

	public static function calculateCo2Savings($gasSavings){
		$result = $gasSavings * Kengetallen::CO2_SAVING_GAS;
		self::debug("CO2 besparing: " . $result . " = " . $gasSavings . " * " . Kengetallen::CO2_SAVING_GAS);
		return $result;
	}

	public static function calculateMoneySavings($gasSavings){
		$result = $gasSavings * Kengetallen::EURO_SAVINGS_GAS;
		self::debug("Euro's besparing: " . $result . " = " . $gasSavings . " * " . Kengetallen::EURO_SAVINGS_GAS);
		return $result;
	}

	public static function calculateCostIndication($surface, $measureAdvice){
		$measureApplication = MeasureApplication::translated('measure_name', $measureAdvice, 'nl')->first();
		if (!$measureApplication instanceof MeasureApplication) return 0;

		$result = max($surface * $measureApplication->costs, $measureApplication->minimal_costs);
		self::debug("Cost indication: " . $result . " = max(" . $surface . " * " . $measureApplication->costs . ", " . $measureApplication->minimal_costs . ")");

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
		self::debug("Max saving for building_type " . $buildingType->id . " + element " . $element->id . " = " . $saving . "%");
		$result = $usage * $saving;
		self::debug($result . " = " . $usage . " * " . $saving);
		return $result;
	}

	protected static function debug($line){
		\Log::debug($line);
	}
}