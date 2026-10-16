<?php

namespace App\Services\SmartTwin;

use App\Models\Building;
use App\Models\InputSource;
use App\Models\SmartTwinPackage;
use App\Models\UserActionPlanAdvice;
use App\Services\UserActionPlanAdviceService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * The woonplan page of the report once the woonplan is SmartTwin's: what the woonplan shows
 * (decision 2026-10-15-smarttwin-rapport-na-omslagpunt, P1).
 *
 * - "Nu aanpakken": "Gekozen pakket", the same lines, amounts and results as under the woonplan's
 *   columns (PackageResults), including the notice when the woonplan no longer holds the package.
 * - "Later uitvoeren": every card with its costs.
 * - "In orde": every card, with the date and description of a measure carried out.
 *
 * No saving per measure: SmartTwin calculates the saving for the package as a whole.
 */
final readonly class WoonplanReport
{
    /**
     * @param  array<int, array{name: string, from: float|int|null, to: float|int|null}>  $later
     * @param  array<int, array{name: string, executed_at: ?Carbon, description: ?string}>  $complete
     */
    public function __construct(
        public PackageResults $results,
        public array $later,
        public array $complete,
    )
    {
    }

    /**
     * @param  Collection<string, Collection<int, UserActionPlanAdvice>>  $categorized  The woonplan's
     *         visible cards by category, in their order, as the report reads them. Read before the
     *         report formats their costs.
     */
    public static function from(Building $building, Collection $categorized): self
    {
        $card = fn (UserActionPlanAdvice $advice): array => [
            'name'  => (string) $advice->userActionPlanAdvisable->name,
            'costs' => CardCosts::of($advice),
            'kind'  => WoonplanCardRules::kindOf($advice),
        ];

        $nowCards = ($categorized[UserActionPlanAdviceService::CATEGORY_TO_DO] ?? collect())->map($card)->values()->all();

        $package = SmartTwinPackage::where('building_id', $building->id)->first();
        // As the woonplan decides it: on master, the visible cards included or not, isIntactIn decides.
        $intact = $package instanceof SmartTwinPackage && $package->isIntactIn(
            UserActionPlanAdvice::withoutGlobalScopes()
                ->where('user_id', $building->user_id)
                ->where('input_source_id', InputSource::master()->id)
                ->get(),
        );

        $later = ($categorized[UserActionPlanAdviceService::CATEGORY_LATER] ?? collect())
            ->map(function (UserActionPlanAdvice $advice) use ($card) {
                $data = $card($advice);

                return ['name' => $data['name'], 'from' => $data['costs']['from'], 'to' => $data['costs']['to']];
            })
            ->values()
            ->all();

        $complete = ($categorized[UserActionPlanAdviceService::CATEGORY_COMPLETE] ?? collect())
            ->map(fn (UserActionPlanAdvice $advice) => [
                'name'        => (string) $advice->userActionPlanAdvisable->name,
                'executed_at' => $advice->isExecuted() ? Carbon::parse($advice->executed_at) : null,
                'description' => $advice->executed_description,
            ])
            ->values()
            ->all();

        return new self(PackageResults::from($package, $intact, $nowCards), $later, $complete);
    }
}
