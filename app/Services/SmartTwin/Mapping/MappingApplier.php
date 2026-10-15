<?php

namespace App\Services\SmartTwin\Mapping;

use App\Enums\AdviceSource;
use App\Enums\SmartTwin\MappingStatus;
use App\Enums\SmartTwin\MappingTarget;
use App\Helpers\NumberFormatter;
use App\Models\Building;
use App\Models\BuildingRoofType;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\RoofType;
use App\Models\SmartTwinPackage;
use App\Models\ToolQuestion;
use App\Models\UserActionPlanAdvice;
use App\Services\SmartTwin\SharedWoonplan;
use App\Services\ToolQuestionService;
use App\Services\UserActionPlanAdviceService;
use Illuminate\Support\Facades\Log;

/**
 * Writes what a mapper decided.
 *
 * Split from the walk so deciding and writing stay apart: a mapper says where a value belongs, this
 * puts it there. That is what lets a mapper be tested without a database, and it keeps every write
 * in the mapping going through one place.
 *
 * Two kinds of thing get written, see App\Enums\SmartTwin\MappingTarget. Answers go through
 * ToolQuestionService, never into a table directly — it owns save_in resolution, merging into
 * `extra.*` JSON columns, clearing answers whose conditions no longer hold, and the copy to the
 * master input source. A straight insert would quietly skip all of it, and the dossier would be
 * subtly wrong in ways that only surface much later. It is safe from a queued job: the same service
 * is called this way from MapQuickScanSituationToExpert.
 */
class MappingApplier
{
    /**
     * The measures this run has priced so far, by id. A second solution for one of them adds to the
     * advice instead of replacing it.
     *
     * @var array<int, true>
     */
    private array $pricedThisRun = [];

    /**
     * Whether this run cleared the action plan already; see clearActionPlanOnce().
     */
    private bool $clearedThisRun = false;

    public function __construct(private readonly SharedWoonplan $sharedWoonplan)
    {
    }

    /**
     * Records the package this import put in the action plan, with what SmartTwin calculated for it:
     * the gas use and energy label before and after, the total cost, the monthly saving and the
     * subsidy. Those are only meaningful for the package as a whole; see SmartTwinPackage.
     *
     * Called once the solutions are written, so the package is the measures that actually reached
     * the plan — a solution without a coupled measure is not part of it.
     *
     * @param  array<string, mixed>  $results
     */
    public function recordPackage(Building $building, InputSource $inputSource, array $results): SmartTwinPackage
    {
        return SmartTwinPackage::updateOrCreate(
            ['building_id' => $building->id],
            [
                'input_source_id'               => $inputSource->id,
                'measure_application_ids'       => array_keys($this->pricedThisRun),
                'current_result'                => $results['current']['calculationResult'] ?? null,
                'scenario_result'               => $results['scenario']['calculationResult'] ?? null,
                'total_cost_with_tax'           => $results['scenario']['totalCostWithTax'] ?? null,
                'energy_cost_savings_per_month' => $results['scenario']['energyCostSavingsPerMonth'] ?? null,
                'total_subsidy'                 => $results['scenario']['totalSubsidy'] ?? null,
                'subsidies'                     => $this->subsidiesOf($results),
                'imported_at'                   => now(),
            ],
        );
    }

    /**
     * Adds an "In orde" card for every element that is in good shape and not advised (see
     * InOrderCards). Called once the package is written, so what is advised is known.
     *
     * A card of SmartTwin's, without a price: there is nothing to do. Not for a measure the resident
     * already carried out; that card is in "In orde" already, with its record.
     */
    public function addInOrderCards(Building $building, InputSource $inputSource): void
    {
        $advised = MeasureApplication::whereIn('id', array_keys($this->pricedThisRun))->pluck('short')->all();

        foreach (app(InOrderCards::class)->for($building, $advised) as $measureShort) {
            $measure = MeasureApplication::findByShort($measureShort);

            if (! $measure instanceof MeasureApplication || $this->isExecuted($building, $measure)) {
                continue;
            }

            $advice = UserActionPlanAdvice::withoutGlobalScopes()->updateOrCreate(
                [
                    'user_id'                         => $building->user_id,
                    'input_source_id'                 => $inputSource->id,
                    'user_action_plan_advisable_type' => MeasureApplication::class,
                    'user_action_plan_advisable_id'   => $measure->id,
                ],
                [
                    'step_id'             => $measure->step_id,
                    'costs'               => ['from' => null, 'to' => null],
                    'source'              => AdviceSource::SMART_TWIN,
                    'category'            => UserActionPlanAdviceService::CATEGORY_COMPLETE,
                    'visible'             => true,
                    'extra'               => null,
                    'savings_gas'         => null,
                    'savings_electricity' => null,
                    'savings_money'       => null,
                ],
            );

            $this->sharedWoonplan->copyFromMaster($advice);
        }
    }

    private function isExecuted(Building $building, MeasureApplication $measure): bool
    {
        return UserActionPlanAdvice::withoutGlobalScopes()
            ->where('user_id', $building->user_id)
            ->where('user_action_plan_advisable_type', MeasureApplication::class)
            ->where('user_action_plan_advisable_id', $measure->id)
            ->whereNotNull('executed_at')
            ->exists();
    }

    /**
     * The subsidy per scheme, in cents like the package's own amounts (decimal(10,2)): SmartTwin
     * sends a dozen decimals, which mean nothing to anyone (Patrick, 15 October 2026).
     *
     * @param  array<string, mixed>  $results
     * @return array<int, array{name: string|null, amount: float|null}>|null
     */
    private function subsidiesOf(array $results): ?array
    {
        $subsidies = $results['scenario']['subsidies'] ?? null;

        if (! is_array($subsidies)) {
            return null;
        }

        return array_values(array_map(fn (mixed $subsidy) => [
            'name'   => is_array($subsidy) ? ($subsidy['name'] ?? null) : null,
            'amount' => is_array($subsidy) && is_numeric($subsidy['amount'] ?? null) ? round((float) $subsidy['amount'], 2) : null,
        ], $subsidies));
    }

    /**
     * Removes the row of a roof shape the dwelling no longer has, here and in master.
     *
     * Deleted model by model rather than in one query: GetMyValuesTrait takes the master copy along
     * on the delete event.
     */
    private function removeRoofType(Building $building, InputSource $inputSource, MappingResult $result): MappingResult
    {
        $roofType = RoofType::findByShort($result->target);

        if (! $roofType instanceof RoofType) {
            return MappingResult::targetMissing($result->target, null, $result->note, MappingTarget::ROOF_TYPE_REMOVAL);
        }

        BuildingRoofType::allInputSources()
            ->where('building_id', $building->id)
            ->where('input_source_id', $inputSource->id)
            ->where('roof_type_id', $roofType->id)
            ->get()
            ->each(fn (BuildingRoofType $row) => $row->delete());

        return $result;
    }

    /**
     * Starts a run over one response: nothing priced yet, and the action plan not yet cleared.
     */
    public function startRun(): void
    {
        $this->pricedThisRun = [];
        $this->clearedThisRun = false;
    }

    /**
     * Clears the action plan for the package this import writes, right before its first advice.
     *
     * The calculated advices SmartTwin replaces go, and so does the package of a previous import:
     * the plan becomes exactly the solutions of this response. Small measures and the resident's
     * and cooperation's own measures stay, made equal for every input source. This happens for
     * every input source, master included: the woonplan is the same for resident and coach once
     * SmartTwin made it (see SharedWoonplan), so a stale row of the other one cannot conflict with
     * it later.
     *
     * Only once an advice actually comes in. A response that advises nothing leaves the plan as it
     * was, rather than emptying it and inviting the resident to do the check they just did.
     */
    private function clearActionPlanOnce(Building $building): void
    {
        if ($this->clearedThisRun) {
            return;
        }

        $this->clearedThisRun = true;

        $replaced = UserActionPlanAdviceService::removeAdvicesReplacedBySmartTwin($building);
        $previous = UserActionPlanAdviceService::removeSmartTwinAdvices($building);

        // What stays, the small and own measures, may still differ per input source: before the
        // switch every input source kept its own rows. From here on they are one woonplan.
        $this->sharedWoonplan->equalizeToMaster($building);

        Log::debug('SmartTwin advice replaced the action plan', [
            'building_id' => $building->getKey(),
            'calculated'  => $replaced,
            'smart_twin'  => $previous,
        ]);
    }

    /**
     * Returns the result as it should be reported, which is not always the one handed in: a mapping
     * that points at something that does not exist comes back as TARGET_MISSING. Anything other
     * than MAPPED passes through untouched — there is nothing to write.
     */
    public function apply(Building $building, InputSource $inputSource, MappingResult $result): MappingResult
    {
        // Anything but a mapped result has nowhere to go, and mapped() guarantees a target — the
        // null check is there so the type tells the same story the constructor does.
        if (MappingStatus::MAPPED !== $result->status || is_null($result->target)) {
            return $result;
        }

        return match ($result->kind) {
            MappingTarget::TOOL_QUESTION     => $this->saveAnswer($building, $inputSource, $result),
            MappingTarget::ADVICE            => $this->saveAdvice($building, $inputSource, $result),
            MappingTarget::ROOF_TYPE_REMOVAL => $this->removeRoofType($building, $inputSource, $result),
        };
    }

    private function saveAnswer(Building $building, InputSource $inputSource, MappingResult $result): MappingResult
    {
        $toolQuestion = ToolQuestion::findByShort($result->target);

        if (! $toolQuestion instanceof ToolQuestion) {
            // A short that does not resolve is a bug in the mapping, not a gap in it, so it is
            // reported as such instead of silently writing nothing.
            return MappingResult::targetMissing($result->target, $result->value, $result->note);
        }

        ToolQuestionService::init()
            ->toolQuestion($toolQuestion)
            ->building($building)
            ->currentInputSource($inputSource)
            ->save($result->value);

        return $result;
    }

    /**
     * Puts an advised measure in the action plan, marked as ours rather than the calculation's.
     *
     * It starts in "Nu aanpakken" and visible, whatever the category logic would make of the current
     * situation: the package is what SmartTwin calculated its saving and label for, and those only
     * hold while the plan holds the package. Left to the category logic, a roof at Rc 1,3 counts as
     * insulated and its advice would land in "In orde" straight away.
     *
     * Two solutions for one measure add up to one advice — an action plan holds one advice per
     * measure, and replacing would silently drop the first price. It has not been seen in practice,
     * so it is logged where someone will notice.
     */
    private function saveAdvice(Building $building, InputSource $inputSource, MappingResult $result): MappingResult
    {
        $measure = MeasureApplication::findByShort($result->target);

        if (! $measure instanceof MeasureApplication) {
            return MappingResult::targetMissing(
                $result->target,
                $result->value,
                $result->note,
                MappingTarget::ADVICE,
            );
        }

        $this->clearActionPlanOnce($building);

        $where = [
            'user_id'                         => $building->user->id,
            'input_source_id'                 => $inputSource->id,
            'user_action_plan_advisable_type' => MeasureApplication::class,
            'user_action_plan_advisable_id'   => $measure->id,
        ];

        $costs = (float) $result->value;
        $details = $result->details;

        if (isset($this->pricedThisRun[$measure->id])) {
            $earlier = UserActionPlanAdvice::withoutGlobalScopes()->where($where)->first();
            // Added up exactly: SmartTwin's amounts are never rounded, and floating point would add
            // noise of its own.
            $earlierCosts = (float) ($earlier?->costs['to'] ?? $earlier?->costs['from'] ?? 0);
            $costs = (float) bcadd(
                NumberFormatter::plain($costs),
                NumberFormatter::plain($earlierCosts),
                max(NumberFormatter::decimalsOf($costs), NumberFormatter::decimalsOf($earlierCosts)),
            );
            $earlierDetails = $earlier?->smartTwinDetails();
            $details = is_null($earlierDetails) ? $details : (is_null($details) ? $earlierDetails : $earlierDetails->with($details));

            Log::warning('SmartTwin advises one measure through more than one solution; their costs were added up', [
                'building_id' => $building->getKey(),
                'measure'     => $measure->short,
            ]);

            $result = MappingResult::mappedAdvice(
                $measure->short,
                $costs,
                trim(($result->note ?? '') . '; opgeteld bij een eerdere solution voor dezelfde maatregel', '; '),
                $details,
            );
        }

        $advice = UserActionPlanAdvice::withoutGlobalScopes()->updateOrCreate(
            $where,
            [
                'step_id'  => $measure->step_id,
                'costs'    => UserActionPlanAdviceService::formatCosts($costs),
                'source'   => AdviceSource::SMART_TWIN,
                'category' => UserActionPlanAdviceService::CATEGORY_TO_DO,
                'visible'  => true,
                'extra'    => $details?->intoExtra(null),
                // Explicitly cleared rather than left alone. SmartTwin gives no saving per measure,
                // and a figure the calculation derived earlier would sit next to a price it knows
                // nothing about. The woonplan card leaves an unknown saving out.
                'savings_gas'         => null,
                'savings_electricity' => null,
                'savings_money'       => null,
            ],
        );

        // The flow's row and master are written; the other input source gets the same. A SmartTwin
        // advice is one woonplan for resident and coach alike.
        $this->sharedWoonplan->copyFromMaster($advice);

        $this->pricedThisRun[$measure->id] = true;

        return $result;
    }
}
