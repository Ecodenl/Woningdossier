<?php

namespace App\Services\SmartTwin;

use App\Models\Building;
use App\Models\CustomMeasureApplication;
use App\Models\InputSource;
use App\Models\Step;
use App\Models\UserActionPlanAdvice;
use Illuminate\Support\Collection;

/**
 * Keeps the woonplan the same for resident, coach and master once it went over to SmartTwin.
 *
 * Before the switch (Hoomdossier::smartTwinOwnsWoonplanFor()) a woonplan action writes the row of
 * whoever acts, and GetMyValuesTrait copies that row to master, the one that is shown. After the
 * switch every action applies to all three. Rather than teaching every action to write three rows,
 * each keeps writing as it did, and then hands the result to this class, which makes the resident's
 * and the coach's row equal to master. One place decides what "the same" means.
 *
 * Writes here fire no model events on purpose. The trait would copy the row straight back to
 * master, and the creating observer would price or file a row that only mirrors one already priced
 * and filed.
 */
class SharedWoonplan
{
    /**
     * The columns that differ per input source by nature, and are never copied.
     */
    private const OWN_COLUMNS = ['id', 'input_source_id', 'created_at', 'updated_at'];

    /**
     * Makes the resident's and the coach's row of this advice equal to the master row, creating them
     * where they are missing. Any row of the advice may be passed in; master is looked up.
     */
    public function copyFromMaster(UserActionPlanAdvice $advice): void
    {
        $master = $this->masterRowOf($advice);

        if (! $master instanceof UserActionPlanAdvice) {
            return;
        }

        foreach ($this->sharingInputSources() as $inputSource) {
            $this->copyTo($master, $inputSource);
        }
    }

    /**
     * Makes the whole woonplan of the building equal to master: every master row is copied to the
     * resident and the coach, and their rows without a master counterpart are removed. Limited to
     * one step when given, for after a recalculation of that step.
     */
    public function equalizeToMaster(Building $building, ?Step $step = null): void
    {
        $masterRows = $this->rows($building, InputSource::master(), $step);

        foreach ($masterRows as $master) {
            foreach ($this->sharingInputSources() as $inputSource) {
                $this->copyTo($master, $inputSource);
            }
        }

        $kept = $masterRows->map(fn (UserActionPlanAdvice $row) => $this->keyOf($row))->all();

        foreach ($this->sharingInputSources() as $inputSource) {
            $this->rows($building, $inputSource, $step)
                ->reject(fn (UserActionPlanAdvice $row) => in_array($this->keyOf($row), $kept, true))
                ->each(fn (UserActionPlanAdvice $row) => $row->deleteQuietly());
        }
    }

    /**
     * @return array<int, InputSource>
     */
    private function sharingInputSources(): array
    {
        return [InputSource::resident(), InputSource::coach()];
    }

    private function copyTo(UserActionPlanAdvice $master, InputSource $inputSource): void
    {
        $advisableId = $this->advisableIdFor($master, $inputSource);

        if (is_null($advisableId)) {
            return;
        }

        $attributes = array_diff_key($master->getAttributes(), array_flip(self::OWN_COLUMNS));
        $attributes['input_source_id'] = $inputSource->id;
        $attributes['user_action_plan_advisable_id'] = $advisableId;

        $row = $this->findRow($master, $inputSource, $advisableId) ?? new UserActionPlanAdvice();
        // Raw: these are master's stored values. Filling them through the casts would encode the
        // JSON columns (costs, extra) a second time. Not synced as original, so an update only
        // writes what differs.
        $row->setRawAttributes(array_merge($row->getAttributes(), $attributes));
        $row->saveQuietly();
    }

    private function findRow(UserActionPlanAdvice $master, InputSource $inputSource, int $advisableId): ?UserActionPlanAdvice
    {
        return UserActionPlanAdvice::withoutGlobalScopes()
            ->where('user_id', $master->user_id)
            ->where('input_source_id', $inputSource->id)
            ->where('user_action_plan_advisable_type', $master->user_action_plan_advisable_type)
            ->where('user_action_plan_advisable_id', $advisableId)
            ->first();
    }

    /**
     * The master row of an advice, the one the woonplan shows, from any of its rows.
     */
    public function masterRowOf(UserActionPlanAdvice $advice): ?UserActionPlanAdvice
    {
        $master = InputSource::master();

        if ($advice->input_source_id === $master->id) {
            return $advice;
        }

        $advisableId = $this->advisableIdFor($advice, $master);

        return is_null($advisableId) ? null : $this->findRow($advice, $master, $advisableId);
    }

    /**
     * The advisable an input source's row points at. The same model for a measure application or a
     * cooperation measure; for an own measure the sibling in that input source, which shares the
     * hash and is created when it does not exist yet.
     */
    private function advisableIdFor(UserActionPlanAdvice $advice, InputSource $inputSource): ?int
    {
        if (CustomMeasureApplication::class !== $advice->user_action_plan_advisable_type) {
            return (int) $advice->user_action_plan_advisable_id;
        }

        $own = CustomMeasureApplication::allInputSources()->find($advice->user_action_plan_advisable_id);

        if (! $own instanceof CustomMeasureApplication) {
            return null;
        }

        $sibling = $own->getSibling($inputSource);

        if (! $sibling instanceof CustomMeasureApplication) {
            $sibling = $own->replicate();
            $sibling->input_source_id = $inputSource->id;
            $sibling->saveQuietly();
        }

        return (int) $sibling->id;
    }

    /**
     * What identifies an advice across input sources: the measure, or the hash of an own measure.
     */
    private function keyOf(UserActionPlanAdvice $row): string
    {
        if (CustomMeasureApplication::class === $row->user_action_plan_advisable_type) {
            $hash = CustomMeasureApplication::allInputSources()->find($row->user_action_plan_advisable_id)?->hash;

            return CustomMeasureApplication::class . '|' . $hash;
        }

        return $row->user_action_plan_advisable_type . '|' . $row->user_action_plan_advisable_id;
    }

    /**
     * @return Collection<int, UserActionPlanAdvice>
     */
    private function rows(Building $building, InputSource $inputSource, ?Step $step): Collection
    {
        return UserActionPlanAdvice::withoutGlobalScopes()
            ->where('user_id', $building->user_id)
            ->where('input_source_id', $inputSource->id)
            ->when($step instanceof Step, fn ($query) => $query->where('step_id', $step->id))
            ->get();
    }
}
