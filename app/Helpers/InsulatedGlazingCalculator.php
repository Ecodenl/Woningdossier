<?php

namespace App\Helpers;

use App\Models\BuildingHeating;
use App\Models\ElementValue;
use App\Models\InsulatingGlazing;
use App\Models\Interest;
use App\Models\KeyFigureTemperature;
use App\Models\MeasureApplication;
use App\Models\PaintworkStatus;
use App\Models\WoodRotStatus;
use Carbon\Carbon;

class InsulatedGlazingCalculator {

	public static function calculateCosts(MeasureApplication $measureApplication, Interest $interest, $m2, $windows) {
		if ($windows <= 0) { return 0; }
		if ($interest->calculate_value > 3) { return 0; }
		if ($m2 <= 0) { return 0; }
		$m2PerWindow = max(1, $m2 / $windows);

		self::debug(__METHOD__ . " m2PerWindow (" . $m2PerWindow . ") = " . $m2 . " / " . $windows);
		$calcM2 = $windows * $m2PerWindow;
		self::debug(__METHOD__ . " " . $calcM2 . " = " . $windows . " * " . $m2PerWindow);

		return Calculator::calculateMeasureApplicationCosts($measureApplication, $calcM2);
	}

	// in m3 per year
	public static function calculateGasSavings($m2, MeasureApplication $measureApplication, BuildingHeating $heating, InsulatingGlazing $glazing = null){
		$query = KeyFigureTemperature::where('measure_application_id', $measureApplication->id)
				->where('building_heating_id', $heating->id);
		if ($glazing instanceof InsulatingGlazing){
			$query->where('insulating_glazing_id', $glazing->id);
		}
		$keyFigureTemperature = $query->first();

		$saving = $m2 * $keyFigureTemperature->key_figure;

		self::debug("Gas saving " . $saving . " = " . $m2 . " * " . $keyFigureTemperature->key_figure);

		return $saving;
	}

	public static function calculatePaintworkSurface(ElementValue $frame, array $woodElements, $windowSurface){
		$number = $frame->calculate_value * $windowSurface;
		self::debug(__METHOD__ . " " . $number . " = " . $frame->calculate_value . " * " . $windowSurface);
		foreach ($woodElements as $woodElement) {
			$number += $woodElement->calculate_value;
			self::debug($woodElement->calculate_value . " -> " . $number);
		}

		return $number;
	}

	public static function determineApplicationYear(MeasureApplication $measureApplication, PaintworkStatus $paintworkStatus, WoodRotStatus $woodRotStatus, $lastPaintedYear){
		self::debug(__METHOD__);

		if ($lastPaintedYear + $measureApplication->maintenance_interval <= Carbon::now()->year){
			self::debug("Last painted is longer than " . $measureApplication->maintenance_interval . " years ago.");
			$year = Carbon::now()->year;
		}
		else {
			$year = $lastPaintedYear + $measureApplication->maintenance_interval;
		}

		// If the paintworks status requires earlier action, that's the year
		$year = min($year, (Carbon::now()->year + $paintworkStatus->calculate_value - 1));
		// If the woodrot status requires earlier action, that's the year
		if (!is_null($woodRotStatus->calculate_value)){
			$year = min($year, (Carbon::now()->year + $woodRotStatus->calculate_value - 1));
		}

		return $year;
	}

	protected static function debug($line){
		\Log::debug($line);
	}
}