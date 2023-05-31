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
     *
     * @param $comment
     * @param null $short
     */
    public static function save(Building $building, InputSource $inputSource, Step $step, $comment, $short = null)
    {
        $dataToUpdate = [
            'input_source_id' => $inputSource->id,
            'step_id' => $step->id,
            'building_id' => $building->id,
        ];

        is_null($short) ?: $dataToUpdate['short'] = $short;

        // we could use the relationships and stuff but then the method isn't testable
        StepComment::withOutGlobalScopes()->updateOrCreate(
            $dataToUpdate,
            [
                'comment' => (new HtmlSanitizer())->sanitize($comment ?? ''),
            ]
        );
    }
}
