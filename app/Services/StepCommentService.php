<?php

namespace App\Services;

use App\Helpers\Sanitizers\HtmlSanitizer;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\Step;
use App\Models\StepComment;

class StepCommentService
{
    /**
     * Method to save step comment(s).
     */
    public static function save(Building $building, InputSource $inputSource, Step $step, ?string $comment): void
    {
        // TODO: Deprecate after deprecating the legacy expert steps
        $dataToUpdate = [
            'input_source_id' => $inputSource->id,
            'step_id' => $step->id,
            'building_id' => $building->id,
        ];

        // we could use the relationships and stuff but then the method isn't testable
        StepComment::withOutGlobalScopes()->updateOrCreate(
            $dataToUpdate,
            [
                'comment' => (new HtmlSanitizer())->sanitize($comment ?? ''),
            ]
        );
    }
}
