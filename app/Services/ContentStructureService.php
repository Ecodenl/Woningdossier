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

        // First, remove all unnecessary fields
        foreach ($contentStructure as $step => $fields) {
            $contentStructure[$step] = Arr::where($fields, function ($trans, $field) {
                // Questions are prefixed with "question_", so we remove that so we can check if this question
                // should be in the CSV. Calculations are prefixed with "calculation_" and we for sure don't need those.
                return ! in_array(str_replace('question_', '', $field), ExampleBuildingHelper::UNANSWERABLE_TOOL_QUESTIONS)
                    && ! Str::startsWith($field, 'calculation_');
            });
        }

        // Secondly, flatten it and remove unnecessary prefix
        $finalStructure = [];
        foreach($contentStructure as $step => $fields) {
            foreach ($fields as $short => $lang) {
                $finalStructure[str_replace('question_', '', $short)] = $lang;
            }
        }

        return $finalStructure;
    }
}