<?php

namespace Tests\Feature\app\Services\SmartTwin;

use App\Enums\MappingType;
use App\Models\MeasureApplication;
use App\Models\SmartTwinSolution;
use App\Services\MappingService;

/**
 * Couples a SmartTwin solution to a measure the way the coupling screen does: import the product,
 * then hang a mapping off it.
 */
trait CouplesSolutions
{
    private function couple(string $solutionId, string $measureShort): void
    {
        $solution = SmartTwinSolution::firstOrCreate(
            ['external_id' => $solutionId],
            ['name' => $solutionId, 'kind' => SmartTwinSolution::kindOf($solutionId), 'last_seen_at' => now()],
        );

        MappingService::init()
            ->from($solution)
            ->type(MappingType::SMARTTWIN_SOLUTION_MEASURE_APPLICATION->value)
            ->sync(
                [MeasureApplication::findByShort($measureShort)],
                MappingType::SMARTTWIN_SOLUTION_MEASURE_APPLICATION->value,
            );
    }
}
