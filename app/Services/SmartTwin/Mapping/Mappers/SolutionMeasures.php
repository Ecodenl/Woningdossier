<?php

namespace App\Services\SmartTwin\Mapping\Mappers;

use App\Models\MeasureApplication;
use App\Models\SmartTwinSolution;
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
 * The response carries an id; a coupling hangs off the imported product that id belongs to. So an
 * id the catalogue does not hold resolves to nothing, exactly as an uncoupled one does — both mean
 * no measure reaches the woonplan, and both are reported rather than guessed at.
 *
 * A solution id reads as `<kind of measure>|<provider>:<product>`, and the products of one kind are
 * usually the same measure to us. That grouping belongs in the screen where the couplings are made,
 * not here: reading it would make the lookup depend on an id format SmartTwin has never documented,
 * for a convenience the screen can offer just as well.
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

        $solution = SmartTwinSolution::firstWhere('external_id', $solutionId);

        if (! $solution instanceof SmartTwinSolution) {
            return null;
        }

        $measure = MappingService::init()
            ->from($solution)
            ->type(SmartTwinSolution::mappingType())
            ->resolveTarget()
            ->first();

        return $measure instanceof MeasureApplication ? $measure->short : null;
    }
}
