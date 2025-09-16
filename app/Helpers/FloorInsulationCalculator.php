<?php

namespace App\Helpers;

use App\Helpers\KeyFigures\FloorInsulation\Temperature;
use App\Models\Building;
use App\Models\ElementValue;
use App\Models\InputSource;
use Illuminate\Support\Facades\Log;

class FloorInsulationCalculator
{
    public static function calculateGasSavings(Building $building, InputSource $inputSource, ElementValue $element, $surface, $measureAdvice)
    {
        $result = 0;

        $kengetalEnergySaving = Temperature::energySavingFigureFloorInsulation($measureAdvice);
        self::debug('Kengetal energebesparing = ' . $kengetalEnergySaving);

        if (isset($element->calculate_value) && $element->calculate_value < 3) {
            $result = min(
                $surface * $kengetalEnergySaving,
                RawCalculator::maxGasSavings($building, $inputSource, $element->element)
            );
            self::debug($result . ' = min(' . $surface . ' * ' . $kengetalEnergySaving . ', ' . RawCalculator::maxGasSavings($building, $inputSource, $element->element) . ')');
        } else {
            self::debug('No gas savings..');
        }

        return $result;
    }

    protected static function debug(string $line): void
    {
        if (config('hoomdossier.services.enable_calculation_logging')) {
            Log::channel('calculations')->debug($line);
        }
    }
}
