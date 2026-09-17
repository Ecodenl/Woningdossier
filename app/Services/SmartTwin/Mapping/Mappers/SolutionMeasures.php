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
    /**
     * A solution id reads as `<what kind of measure>|<provider>:<which product>`, for example
     * `Insulate Facade Cavity|SmartTwin:Cavity_Insulation_EPS_Pearls`.
     */
    private const PROVIDER_SEPARATOR = '|';

    /**
     * Two ways to reach a measure, tried in this order.
     *
     * The full id first: that is a coupling for one specific product, and the most specific answer
     * should win. The part before the provider second: that is the kind of measure, which is what
     * the sync command couples, and it covers every product of that kind at once — including the
     * ones SmartTwin adds after the last sync.
     *
     * So a new EPS variant works the day it appears, and a product that turns out to need its own
     * measure can be given one without disturbing the rest.
     */
    public function shortFor(string $solutionId): ?string
    {
        return $this->lookUp($solutionId) ?? $this->lookUp($this->kindOf($solutionId));
    }

    /**
     * The part of the id that says what kind of measure it is, or null when the id carries no
     * provider at all — then there is nothing more general to fall back to.
     */
    public function kindOf(string $solutionId): ?string
    {
        if (! str_contains($solutionId, self::PROVIDER_SEPARATOR)) {
            return null;
        }

        return trim(explode(self::PROVIDER_SEPARATOR, $solutionId, 2)[0]);
    }

    private function lookUp(?string $fromValue): ?string
    {
        if (is_null($fromValue) || '' === $fromValue) {
            return null;
        }

        $measure = MappingService::init()
            ->from($fromValue)
            ->type(MappingType::SMARTTWIN_SOLUTION_MEASURE_APPLICATION->value)
            ->resolveTarget()
            ->first();

        return $measure instanceof MeasureApplication ? $measure->short : null;
    }
}
