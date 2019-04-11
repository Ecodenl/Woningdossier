<?php

namespace App\Http\Controllers\Cooperation\Admin\SuperAdmin;

use App\Helpers\Calculation\BankInterestCalculator;
use App\Helpers\Kengetallen;
use App\Helpers\KeyFigures as KeyFigures;
use App\Models\Cooperation;
use App\Models\MeasureApplication;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class KeyFiguresController extends Controller
{
    public function index(Cooperation $cooperation)
    {
	    // we handle translations in the view.
    	$keyfigures = [
    		'general' => (new \ReflectionClass(Kengetallen::class))->getConstants(),
		    'heater' => (new \ReflectionClass(KeyFigures\Heater\KeyFigures::class))->getConstants(),
		    'pv-panels' => (new \ReflectionClass(KeyFigures\PvPanels\KeyFigures::class))->getConstants(),
	    ];

    	// Bank
    	$keyfigures['general']['BANK_INTEREST_PER_YEAR'] = BankInterestCalculator::BANK_INTEREST_PER_YEAR;
    	$keyfigures['general']['INTEREST_PERIOD'] = BankInterestCalculator::INTEREST_PERIOD;

    	$keyfigures['wall-insulation'] = KeyFigures\WallInsulation\Temperature::getKeyFigures();
    	$keyfigures['roof-insulation'] = KeyFigures\RoofInsulation\Temperature::getKeyFigures();
    	$keyfigures['floor-insulation'] = KeyFigures\FloorInsulation\Temperature::getKeyFigures();

        $measureApplications = MeasureApplication::all();

        return view('cooperation.admin.super-admin.key-figures.index', compact(
            'keyfigures', 'measureApplications'
        ));
    }
}
