<?php

namespace App\Services\SmartTwin\Mapping;

use App\Enums\AdviceSource;
use App\Enums\SmartTwin\MappingStatus;
use App\Enums\SmartTwin\MappingTarget;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\ToolQuestion;
use App\Models\UserActionPlanAdvice;
use App\Services\ToolQuestionService;
use App\Services\UserActionPlanAdviceService;

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
            MappingTarget::TOOL_QUESTION => $this->saveAnswer($building, $inputSource, $result),
            MappingTarget::ADVICE        => $this->saveAdvice($building, $inputSource, $result),
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
     * updateOrCreate rather than insert, for two reasons. A second scan has to be able to write its
     * new figures over the first. And when the calculation already advised this measure, taking the
     * row over is exactly right: from here on SmartTwin's price is the one the resident sees.
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

        UserActionPlanAdvice::withoutGlobalScopes()->updateOrCreate(
            [
                'user_id'                         => $building->user->id,
                'input_source_id'                 => $inputSource->id,
                'user_action_plan_advisable_type' => MeasureApplication::class,
                'user_action_plan_advisable_id'   => $measure->id,
            ],
            [
                'step_id' => $measure->step_id,
                'costs'   => UserActionPlanAdviceService::formatCosts($result->value),
                'source'  => AdviceSource::SMART_TWIN,
                // Explicitly cleared rather than left alone. SmartTwin gives no saving per measure,
                // and a figure the calculation derived earlier would sit next to a price it knows
                // nothing about. The woonplan card leaves an unknown saving out.
                'savings_gas'         => null,
                'savings_electricity' => null,
                'savings_money'       => null,
            ],
        );

        return $result;
    }
}
