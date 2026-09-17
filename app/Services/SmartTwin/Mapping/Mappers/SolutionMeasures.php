<?php

namespace App\Services\SmartTwin\Mapping\Mappers;

use App\Enums\MappingType;
use App\Models\MeasureApplication;
use App\Services\MappingService;

/**
 * Which measure a SmartTwin solution is.
 *
 * One solution, one measure application. The couplings live in the `mappings` table rather than in
 * code, and they are the only translation in this mapping that does: SmartTwin's enums are fixed by
 * their spec, but its catalogue of solutions is not. A new insulation product arrives as an id we
 * have never seen without anything about the API changing, and a coupling nobody can add without a
 * deploy would mean that measure missing from every action plan until the next release.
 *
 * A solution id reads as `<kind of measure>|<provider>:<product>`, and the products of one kind are
 * usually the same measure to us. That grouping belongs in the screen where the couplings are made,
 * not here: reading it would make the lookup depend on an id format SmartTwin has never documented,
 * for a convenience the screen can offer just as well. An uncoupled id is reported rather than
 * guessed at, so a new product is visible in two places instead of quietly missing from one.
 *
 * Returns the short rather than the model, so a mapper can reason in shorts and the applier does
 * the resolving — the same split as ElementValues.
 */
class SolutionMeasures
{
    public function shortFor(string $solutionId): ?string
    {
        if ('' === $solutionId) {
            return null;
        }

        $measure = MappingService::init()
            ->from($solutionId)
            ->type(MappingType::SMARTTWIN_SOLUTION_MEASURE_APPLICATION->value)
            ->resolveTarget()
            ->first();

        return $measure instanceof MeasureApplication ? $measure->short : null;
    }
}
