<?php

namespace App\Services\SmartTwin\Mapping;

use App\Enums\SmartTwin\MappingStatus;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\ToolQuestion;
use App\Services\ToolQuestionService;

/**
 * Writes what a mapper decided.
 *
 * Split from the walk so deciding and writing stay apart: a mapper says where a value belongs, this
 * puts it there. That is what lets a mapper be tested without a database, and it keeps every write
 * in the mapping going through one place.
 *
 * That place is ToolQuestionService, never a table directly. It owns save_in resolution, merging
 * into `extra.*` JSON columns, clearing answers whose conditions no longer hold, and the copy to
 * the master input source — a straight insert would quietly skip all of it, and the dossier would
 * be subtly wrong in ways that only surface much later. It is safe from a queued job: the same
 * service is called this way from MapQuickScanSituationToExpert.
 */
class MappingApplier
{
    /**
     * Returns the result as it should be reported, which is not always the one handed in: a mapping
     * that points at a tool question that does not exist comes back as TARGET_MISSING. Anything
     * other than MAPPED passes through untouched — there is nothing to write.
     */
    public function apply(Building $building, InputSource $inputSource, MappingResult $result): MappingResult
    {
        // Anything but a mapped result has nowhere to go, and mapped() guarantees a target — the
        // null check is there so the type tells the same story the constructor does.
        if (MappingStatus::MAPPED !== $result->status || is_null($result->target)) {
            return $result;
        }

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
}
