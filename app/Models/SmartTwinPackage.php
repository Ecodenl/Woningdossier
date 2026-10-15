<?php

namespace App\Models;

use App\Enums\AdviceSource;
use App\Services\UserActionPlanAdviceService;
use App\Helpers\NumberFormatter;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * The package the last SmartTwin import put in a building's action plan, with what SmartTwin
 * calculated for it.
 *
 * SmartTwin gives one saving, one energy label and one subsidy (per scheme) for the package as a
 * whole, not per measure. They only hold while "Nu aanpakken" holds exactly that package — move a package card out, trash it, or
 * add another one, and the plan is no longer what SmartTwin calculated. Whether the package is still
 * intact is asked of the plan as it is shown, never stored: put the card back and it is intact again.
 *
 * @property int $id
 * @property int $building_id
 * @property int|null $input_source_id
 * @property array<int, int> $measure_application_ids
 * @property array<string, mixed>|null $current_result
 * @property array<string, mixed>|null $scenario_result
 * @property float|null $total_cost_with_tax
 * @property float|null $energy_cost_savings_per_month
 * @property float|null $total_subsidy
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
            // As SmartTwin sent them: its amounts are never rounded.
            'total_cost_with_tax'           => 'float',
            'energy_cost_savings_per_month' => 'float',
            'total_subsidy'                 => 'float',
            'subsidies'                     => 'array',
            'imported_at'                   => 'datetime',
        ];
    }

    /**
     * SmartTwin's amounts are never rounded, and a PHP float handed to the database is: PDO writes
     * it with the `precision` setting (14 digits), so 6853.749263548063 lands as 6853.7492635481.
     * Written as the exact notation instead, which a double column keeps as sent.
     */
    protected function totalCostWithTax(): Attribute
    {
        return Attribute::make(set: fn (mixed $value) => self::exact($value));
    }

    protected function energyCostSavingsPerMonth(): Attribute
    {
        return Attribute::make(set: fn (mixed $value) => self::exact($value));
    }

    protected function totalSubsidy(): Attribute
    {
        return Attribute::make(set: fn (mixed $value) => self::exact($value));
    }

    private static function exact(mixed $value): mixed
    {
        return is_float($value) ? NumberFormatter::plain($value) : $value;
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
     * card in "Nu aanpakken", and no other SmartTwin card is there. Own measures and small measures
     * count only when OWN_MEASURES_BREAK_PACKAGE says so. The order of the cards does not matter.
     *
     * @param  iterable<UserActionPlanAdvice>  $advices  The cards of the action plan as shown (master).
     */
    public function isIntactIn(iterable $advices): bool
    {
        $measureIds = [];

        foreach ($advices as $advice) {
            // getAttribute(), not ->visible: from inside a model, ->visible on another model reaches
            // Model::$visible — the serialisation list — rather than the column.
            if (! $advice->getAttribute('visible') || UserActionPlanAdviceService::CATEGORY_TO_DO !== $advice->getAttribute('category')) {
                continue;
            }

            if (MeasureApplication::class === $advice->getAttribute('user_action_plan_advisable_type')
                && AdviceSource::SMART_TWIN === $advice->getAttribute('source')
            ) {
                $measureIds[] = (int) $advice->getAttribute('user_action_plan_advisable_id');
            } elseif (static::OWN_MEASURES_BREAK_PACKAGE) {
                return false;
            }
        }

        return $this->isIntactFor($measureIds);
    }

    /**
     * Whether a "Nu aanpakken" column holding these standard measures is the package: the same
     * measures, no more and no fewer, in whatever order.
     *
     * @param  iterable<int>  $measureApplicationIds  The measures of the visible cards in "Nu aanpakken".
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
