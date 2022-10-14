<?php

namespace App\Services;

use App\Helpers\ExampleBuildingHelper;
use App\Traits\FluentCaller;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ContentStructureService
{
    use FluentCaller;

    protected array $contentStructure;

    public function __construct(array $contentStructure)
    {
        $this->contentStructure = $contentStructure;
    }

    public function applicableForExampleBuildings(): array
    {
        $contentStructure = $this->contentStructure;

        // TODO: Refactor when colleagues give more clarity
        $shortsToSkip = array_merge(ExampleBuildingHelper::UNANSWERABLE_TOOL_QUESTIONS, ExampleBuildingHelper::NOT_IN_CSV);

        foreach ($contentStructure as $step => $fields) {
            $contentStructure[$step] = Arr::where($fields, function ($trans, $field) use ($shortsToSkip) {
                // Questions are prefixed with "question_", so we remove that so we can check if this question
                // should be in the CSV. Calculations are prefixed with "calculation_" and we for sure don't need those.
                // Considerables are also skipped (TODO but why?)
                return ! in_array(str_replace('question_', '', $field), $shortsToSkip)
                    || Str::startsWith($field, 'calculation_') || Str::endsWith($field, 'considerable');
            });
        }

        return $contentStructure;
    }
}