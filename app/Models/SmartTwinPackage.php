<?php

namespace App\Models;

use App\Enums\AdviceSource;
use App\Services\UserActionPlanAdviceService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * The package the last SmartTwin import put in a building's action plan, with what SmartTwin
 * calculated for it.
 *
 * SmartTwin gives one saving, one energy label and one subsidy (per scheme) for the package as a
 * whole, not per measure. They only hold while the woonplan holds exactly that package. A package
 * card moved to "Later uitvoeren" leaves it intact (stuurgroep); trash one, mark one as carried out,
 * or add another SmartTwin card to "Nu aanpakken", and the plan is no longer what SmartTwin
 * calculated. Whether the package is still intact is asked of the plan as it is shown, never stored:
 * put the card back and it is intact again.
 *
 * @property int $id
 * @property int $building_id
 * @property int|null $input_source_id
 * @property array<int, int> $measure_application_ids
 * @property array<string, mixed>|null $current_result
 * @property array<string, mixed>|null $scenario_result
 * @property numeric|null $total_cost_with_tax
 * @property numeric|null $energy_cost_savings_per_month
 * @property numeric|null $total_subsidy
 * @property array<int, array{name: string|null, amount: float|null}>|null $subsidies
 * @property \Illuminate\Support\Carbon $imported_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Building $building
 * @mixin \Eloquent
 */
class SmartTwinPackage extends Model
{
    protected $table = 'smarttwin_packages';

    protected $fillable = [
        'building_id',
        'input_source_id',
        'measure_application_ids',
        'current_result',
        'scenario_result',
        'total_cost_with_tax',
        'energy_cost_savings_per_month',
        'total_subsidy',
        'subsidies',
        'imported_at',
    ];

    protected function casts(): array
    {
        return [
            'measure_application_ids'       => 'array',
            'current_result'                => 'array',
            'scenario_result'               => 'array',
            'total_cost_with_tax'           => 'decimal:2',
            'energy_cost_savings_per_month' => 'decimal:2',
            'total_subsidy'                 => 'decimal:2',
            'subsidies'                     => 'array',
            'imported_at'                   => 'datetime',
        ];
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    /** Gas saved per year by the package, in m³: what the dwelling uses now minus after the package. */
    public function gasSavings(): ?float
    {
        $now = $this->current_result['energyConsumptionGas'] ?? null;
        $after = $this->scenario_result['energyConsumptionGas'] ?? null;

        return is_null($now) || is_null($after) ? null : round((float) $now - (float) $after, 2);
    }

    /** The energy label after the package. */
    public function labelAfter(): ?string
    {
        return $this->scenario_result['energyLabel'] ?? null;
    }

    /**
     * Whether an own measure — the resident's or the cooperation's — in "Nu aanpakken" breaks the
     * package. It does not (Patrick, 7 October 2026; confirmed by the stuurgroep on 8 October): it
     * is not a card SmartTwin calculated, but it does not stop the package from being carried out
     * either. Should that change, flipping this is the whole change.
     *
     * A small measure counts as an own measure here. SmartTwin's advice leaves the small measures in
     * the plan (see UserActionPlanAdviceService::isReplaceableBySmartTwin()): they are what the
     * resident said they do or want to do, not part of what SmartTwin calculated.
     */
    public const OWN_MEASURES_BREAK_PACKAGE = false;

    /**
     * Whether the action plan still holds the package: every package measure has a visible SmartTwin
     * card in "Nu aanpakken" or "Later uitvoeren", and no other SmartTwin card is in "Nu aanpakken".
     * Own measures and small measures count only when OWN_MEASURES_BREAK_PACKAGE says so. The order
     * of the cards does not matter.
     *
     * A SmartTwin card in "Later uitvoeren" that is not part of the package is left out: it is not
     * something the resident is doing now, so it does not change what SmartTwin calculated.
     *
     * @param  iterable<UserActionPlanAdvice>  $advices  The cards of the action plan as shown (master).
     */
    public function isIntactIn(iterable $advices): bool
    {
        $package = array_map('intval', $this->measure_application_ids ?? []);
        $measureIds = [];

        foreach ($advices as $advice) {
            // getAttribute(), not ->visible: from inside a model, ->visible on another model reaches
            // Model::$visible — the serialisation list — rather than the column.
            if (! $advice->getAttribute('visible')) {
                continue;
            }

            $category = $advice->getAttribute('category');
            $measureId = (int) $advice->getAttribute('user_action_plan_advisable_id');
            $fromSmartTwin = MeasureApplication::class === $advice->getAttribute('user_action_plan_advisable_type')
                && AdviceSource::SMART_TWIN === $advice->getAttribute('source');

            if (UserActionPlanAdviceService::CATEGORY_LATER === $category) {
                if ($fromSmartTwin && in_array($measureId, $package, true)) {
                    $measureIds[] = $measureId;
                }

                continue;
            }

            if (UserActionPlanAdviceService::CATEGORY_TO_DO !== $category) {
                continue;
            }

            if ($fromSmartTwin) {
                $measureIds[] = $measureId;
            } elseif (static::OWN_MEASURES_BREAK_PACKAGE) {
                return false;
            }
        }

        return $this->isIntactFor($measureIds);
    }

    /**
     * Whether a plan holding these standard measures is the package: the same measures, no more and
     * no fewer, in whatever order.
     *
     * @param  iterable<int>  $measureApplicationIds  The measures of the visible SmartTwin cards that
     *                                                count for the package; see isIntactIn().
     */
    public function isIntactFor(iterable $measureApplicationIds): bool
    {
        $inPlan = array_map('intval', is_array($measureApplicationIds) ? $measureApplicationIds : iterator_to_array($measureApplicationIds));
        $package = array_map('intval', $this->measure_application_ids ?? []);

        sort($inPlan);
        sort($package);

        return array_values(array_unique($inPlan)) === array_values(array_unique($package));
    }
}
