<?php

namespace App\Services\SmartTwin\Mapping\Mappers;

use App\Enums\MappingType;
use App\Models\MeasureApplication;
use App\Services\MappingService;

/**
 * Which measure a SmartTwin solution is.
 *
 * The lookup lives in the `mappings` table rather than in code, and it is the only translation in
 * this mapping that does. SmartTwin's enums are fixed by their spec, but its catalogue of solutions
 * is not: a new insulation product appears as an id we have never seen without anything about the
 * API changing. A coupling nobody can add without a deploy would mean that measure silently missing
 * from every action plan until the next release.
 *
 * Returns the short rather than the model, so a mapper can reason in shorts and the applier does
 * the resolving — the same split as ElementValues.
 */
class SolutionMeasures
{
    public function shortFor(string $solutionId): ?string
    {
        $measure = MappingService::init()
            ->from($solutionId)
            ->type(MappingType::SMARTTWIN_SOLUTION_MEASURE_APPLICATION->value)
            ->resolveTarget()
            ->first();

        return $measure instanceof MeasureApplication ? $measure->short : null;
    }
}
