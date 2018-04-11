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
use App\Models\PriceIndexing;
use App\Models\UserEnergyHabit;
use Carbon\Carbon;

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
		$measureApplication = MeasureApplication::translated('measure_name', $measureAdvice, 'nl')->first(['measure_applications.*']);
		if (!$measureApplication instanceof MeasureApplication) return 0;

		$result = max($surface * $measureApplication->costs, $measureApplication->minimal_costs);
		self::debug("Cost indication: " . $result . " = max(" . $surface . " * " . $measureApplication->costs . ", " . $measureApplication->minimal_costs . ")");

		return $result;
	}

	/**
	 * Return the costs of applying a particular measure in a particular year.
	 * This takes yearly cost indexing into account.
	 *
	 * @param MeasureApplication $measure
	 * @param $number
	 * @param null $applicationYear
	 *
	 * @return float|int
	 */
	public static function calculateMeasureApplicationCosts(MeasureApplication $measure, $number, $applicationYear = null){
		if ($number <= 0) return 0;
		// if $applicationYear is null, we assume this year.
		if (is_null($applicationYear)){
			$applicationYear = Carbon::now()->year;
		}
		$yearFactor = $applicationYear - Carbon::now()->year;
		if ($yearFactor < 0){
			$yearFactor = 0;
		}

		$total = max($number * $measure->costs, $measure->minimal_costs);
		self::debug(__METHOD__ . " Non indexed costs: " . $total . " = max(" . $number . " * " . $measure->costs . ", " . $measure->minimal_costs . ")");
		// Apply indexing (general indexing which applies for measures)

		$index = PriceIndexing::where('short', 'common')->first();
		// default = 2%
		$costIndex = 2;
		if ($index instanceof PriceIndexing){
			$costIndex = $index->percentage;
		}

		$totalIndexed = $total * pow((1 + ($costIndex / 100)), $yearFactor);

		self::debug(__METHOD__ . " Indexed costs: " . $totalIndexed . " = " . $total . " * " . (1 + ($costIndex / 100)) . "^" . $yearFactor);

		return $totalIndexed;
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
		$result = $usage * ($saving / 100);
		self::debug($result . " = " . $usage . " * " . ($saving / 100));
		return $result;
	}

	protected static function debug($line){
		\Log::debug($line);
	}
}