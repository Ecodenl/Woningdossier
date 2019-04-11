<?php

namespace App\Http\Controllers\Cooperation\Admin\SuperAdmin;

use App\Helpers\Calculation\BankInterestCalculator;
use App\Helpers\Kengetallen;
use App\Helpers\KeyFigures as KeyFigures;
use App\Models\BuildingTypeElementMaxSaving;
use App\Models\Cooperation;
use App\Models\KeyFigureTemperature;
use App\Models\MeasureApplication;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class KeyFiguresController extends Controller {
	public function index( Cooperation $cooperation )
	{
		// we handle translations in the view.
		$keyfigures = [
			'general'   => ( new \ReflectionClass( Kengetallen::class ) )->getConstants(),
			'heater'    => ( new \ReflectionClass( KeyFigures\Heater\KeyFigures::class ) )->getConstants(),
			'pv-panels' => ( new \ReflectionClass( KeyFigures\PvPanels\KeyFigures::class ) )->getConstants(),
		];

		// Bank
		$keyfigures['general']['BANK_INTEREST_PER_YEAR'] = BankInterestCalculator::BANK_INTEREST_PER_YEAR;
		$keyfigures['general']['INTEREST_PERIOD']        = BankInterestCalculator::INTEREST_PERIOD;

		$keyfigures['max-savings'] = $this->maxSavings();

		$keyfigures['wall-insulation']  = KeyFigures\WallInsulation\Temperature::getKeyFigures();
		$keyfigures['roof-insulation']  = KeyFigures\RoofInsulation\Temperature::getKeyFigures();
		$keyfigures['floor-insulation'] = KeyFigures\FloorInsulation\Temperature::getKeyFigures();
		$keyfigures['insulated-glazing'] = $this->keyFiguresInsulatedGlazing();



		$measureApplications = MeasureApplication::all();

		return view( 'cooperation.admin.super-admin.key-figures.index',
			compact(
				'keyfigures',
				'measureApplications'
			) );
	}

	// todo refactor
	protected function keyFiguresInsulatedGlazing()
	{
		$figures = [];

		// Insulated glazing key figures
		$igMeasures = MeasureApplication::whereIn( 'short',
			[
				'glass-in-lead',
				'hrpp-glass-only',
				'hrpp-glass-frames',
				'hr3p-frames',
			] )->get();
		$igmIds = $igMeasures->pluck('id');

		$keyFigureTemperatures = KeyFigureTemperature::whereIn('measure_application_id', $igmIds)->get();

		/** @var KeyFigureTemperature $keyFigureTemperature */
		foreach($keyFigureTemperatures as $keyFigureTemperature){


			$k = sprintf('%s (%s) %s',
				$keyFigureTemperature->measureApplication->measure_name,
				$keyFigureTemperature->insulatingGlazing->name,
				$keyFigureTemperature->buildingHeating->name
			);
			$figures[$k] = $keyFigureTemperature->key_figure;
		}

		return $figures;
	}

	// todo refactor
	protected function maxSavings(){
		$figures = [];

		$maxSavings = BuildingTypeElementMaxSaving::all();
		/** @var BuildingTypeElementMaxSaving $maxSaving */
		foreach($maxSavings as $maxSaving){
			$k = sprintf('%s %s - %s', __('key-figures.max-savings.prefix'), $maxSaving->buildingType->name, $maxSaving->element->name);
			$figures[$k] = $maxSaving->max_saving . '%';
		}

		return $figures;
	}
}
