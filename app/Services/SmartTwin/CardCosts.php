<?php

namespace App\Services\SmartTwin;

use App\Enums\AdviceSource;
use App\Helpers\NumberFormatter;
use App\Models\UserActionPlanAdvice;

/**
 * The costs of a woonplan card, as the card, its line under the columns and the report all show
 * them. SmartTwin's prices are not rounded to the woonplan's steps of five, the woonplan's own still
 * are; all are shown in whole euros (decision 2026-10-15-smarttwin-bedragen-weergave, R1, R4, R5).
 */
final class CardCosts
{
    /**
     * @return array{from: float|int|null, to: float|int|null}  SmartTwin's as a float, the woonplan's own rounded to an int.
     */
    public static function of(UserActionPlanAdvice $advice): array
    {
        $fromSmartTwin = AdviceSource::SMART_TWIN === $advice->source;
        $costs = is_array($advice->costs) ? $advice->costs : [];

        $amount = fn (mixed $value) => empty($value)
            ? null
            : ($fromSmartTwin ? (float) $value : NumberFormatter::round($value));

        return [
            'from' => $amount($costs['from'] ?? null),
            'to'   => $amount($costs['to'] ?? null),
        ];
    }
}
