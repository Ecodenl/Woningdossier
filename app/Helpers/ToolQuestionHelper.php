<?php

namespace App\Helpers;

use App\Helpers\QuestionValues\QuestionValue;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\ToolQuestion;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ToolQuestionHelper {

    /**
     * These tables should query on one or more extra column(s)
     * Order for multiple columns is very important
     */
    const TABLE_COLUMN = [
        'building_elements' => 'element_id',
        'building_services' => 'service_id',
        'step_comments' => [
            'step_id',
            'short',
        ],
    ];

    /**
     * An array map of tool questions that should do a full recalculate on change.
     */
    const TOOL_QUESTION_FULL_RECALCULATE = [
        'thermostat-high',
        'thermostat-low',
        'hours-high',
        'heating-first-floor',
        'heating-second-floor',
        'surface',
        'resident-count',
        'water-comfort',
        'amount-gas',
        'amount-electricity',
    ];

    /**
     * Array map that will link the tool question short to matching step shorts; eg
     * The tool question "current-floor-insulation" will also be questioned on the expert step "floor-insulation"
     * Thus we will map it to floor insulation step.
     */
    const TOOL_QUESTION_STEP_MAP = [
        'roof-type' => ['roof-insulation'],
        'water-comfort' => ['heater'],
        'cook-type' => ['high-efficiency-boiler'],
        'current-wall-insulation' => ['wall-insulation'],
        'current-floor-insulation' => ['floor-insulation'],
        'current-roof-insulation' => ['roof-insulation'],
        'current-living-rooms-windows' => ['insulated-glazing'],
        'current-sleeping-rooms-windows' => ['insulated-glazing'],
        'heat-source' => ['high-efficiency-boiler'],
        'boiler-type' => ['high-efficiency-boiler'],
        'boiler-placed-date' => ['high-efficiency-boiler'],
        'heater-type' => ['heater'],
        'ventilation-type' => ['ventilation'],
        'ventilation-demand-driven' => ['ventilation'],
        'ventilation-heat-recovery' => ['ventilation'],
        'crack-sealing-type' => ['ventilation'],
        'has-solar-panels' => ['solar-panels'],
        'solar-panel-count' => ['solar-panels'],
        'total-installed-power' => ['solar-panels'],
        'solar-panels-placed-date' => ['solar-panels'],
    ];

    /**
     * Array map from tool question to another tool question short, giving the data that should be replaced,
     * and which tool question to get the answer for, for the replaceable.
     */
    const TOOL_QUESTION_ANSWER_REPLACEABLES = [
        // the question where we will replace something.
        'building-type' => [
            // the question that will be used to replace
            'short' => 'building-type-category',
            // the attribute that we will use as a replacer
            'replaceable' => 'name',
        ],
    ];

    public static function stepShortsForToolQuestion(ToolQuestion $toolQuestion): array
    {
        if (isset(self::TOOL_QUESTION_STEP_MAP[$toolQuestion->short])) {
            return self::TOOL_QUESTION_STEP_MAP[$toolQuestion->short];
        }
        return [];
    }

    /**
     * Simple method to determine whether the given tool question should use the old advice on a recalculate
     *
     */
    public static function shouldToolQuestionDoFullRecalculate(ToolQuestion $toolQuestion): bool
    {
        return in_array($toolQuestion->short,self::TOOL_QUESTION_FULL_RECALCULATE, true);
    }

    /**
     * Simple method to resolve the save in to something we can use.
     *
     * @param ToolQuestion $toolQuestion
     * @param Building $building
     * @return array
     */
    public static function resolveSaveIn(ToolQuestion $toolQuestion, Building $building): array
    {
        $savedInParts = explode('.', $toolQuestion->save_in);
        $table = $savedInParts[0];
        $column = $savedInParts[1];
        $where = [];

        if (Schema::hasColumn($table, 'user_id')) {
            $where[] = ['user_id', '=', $building->user_id];
        } else {
            $where[] = ['building_id', '=', $building->id];
        }

        // 2 parts is the simple scenario, this just means a table + column
        // but in some cases it holds more info we need to build wheres.
        if (count($savedInParts) > 2) {
            // In this case the column holds extra where values

            // There's 2 cases. Either it's a single value, or a set of columns
            if (Str::contains($column, '_')) {
                // Set of columns, we set the wheres based on the order of values
                $columns = ToolQuestionHelper::TABLE_COLUMN[$table];
                $values = explode('_', $column);

                // Currently only for step_comments that can have a short
                foreach ($values as $index => $value) {
                    $where[] = [$columns[$index], '=', $value];
                }
            } else {
                // Just a value, but the short table could be an array. We grab the first
                $columns = ToolQuestionHelper::TABLE_COLUMN[$table];
                $columnForWhere = is_array($columns) ? $columns[0] : $columns;

                $where[] = [$columnForWhere, '=', $column];
            }

            $columns = array_slice($savedInParts, 2);
            $column = implode('.', $columns);
        }

        return compact('table', 'column', 'where');
    }

    /**
     * Get a human readable answer.
     *
     * @param  \App\Models\Building  $building
     * @param  \App\Models\InputSource  $inputSource
     * @param  \App\Models\ToolQuestion  $toolQuestion
     * @param  bool  $withIcons
     * @param  null  $answer
     *
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|int|mixed|string|string[]|null
     */
    public static function getHumanReadableAnswer(Building $building, InputSource $inputSource, ToolQuestion $toolQuestion, bool $withIcons = false, $answer = null)
    {
        $humanReadableAnswer = __('cooperation/frontend/tool.no-answer-given');

        if (empty($answer)) {
            $answer = $building->getAnswer($inputSource, $toolQuestion);
        }
        $answer = strip_tags($answer);
//
        if (! empty($answer) || (is_numeric($answer) && (int) $answer === 0)) {
            $questionValues = QuestionValue::getQuestionValues($toolQuestion, $building, $inputSource);

            if ($questionValues->isNotEmpty()) {
                $humanReadableAnswers = [];

                $answer = is_array($answer) ? $answer : [$answer];

                foreach ($answer as $subAnswer) {
                    $questionValue = $questionValues->where('value', '=', $subAnswer)->first();

                    if (! empty($questionValue)) {
                        $answerToAppend = $questionValue['name'];

                        if (! empty($questionValue['extra']['icon']) && $withIcons) {
                            $answerToAppend .= '<i class="ml-1 w-8 h-8 ' . $questionValue['extra']['icon'] . '"></i>';
                        }

                        $humanReadableAnswers[] = $answerToAppend;
                    }
                }

                if (! empty($humanReadableAnswers)) {
                    $humanReadableAnswer = implode(', ', $humanReadableAnswers);
                }
            } else {
                // If there are no question values, then it's user input
                $humanReadableAnswer = $answer;
            }

            // Format answers
            if ($toolQuestion->toolQuestionType->short === 'text' && \App\Helpers\Str::arrContains($toolQuestion->validation, 'numeric')) {
                $isInteger = \App\Helpers\Str::arrContains($toolQuestion->validation, 'integer');
                $humanReadableAnswer = NumberFormatter::formatNumberForUser($humanReadableAnswer, $isInteger);
            } elseif ($toolQuestion->toolQuestionType->short === 'slider') {
                $humanReadableAnswer = str_replace('.', '', NumberFormatter::format($humanReadableAnswer, 0));
            } elseif ($toolQuestion->toolQuestionType->short === 'rating-slider') {
                $humanReadableAnswerArray = json_decode($humanReadableAnswer, true);
                $humanReadableAnswer = [];
                foreach ($toolQuestion->options as $option) {
                    $humanReadableAnswer[$option['name']] = $humanReadableAnswerArray[$option['short']];
                }
            }
        }

        return $humanReadableAnswer;
    }

    /**
     * Handle potential replaceables in a tool question name.
     *
     * @param  \App\Models\Building  $building
     * @param  \App\Models\InputSource  $inputSource
     * @param  \App\Models\ToolQuestion  $toolQuestion
     *
     * @return \App\Models\ToolQuestion
     */
    public static function handleToolQuestionReplaceables(Building $building, InputSource $inputSource, ToolQuestion $toolQuestion): ToolQuestion
    {
        if (\App\Helpers\Str::hasReplaceables($toolQuestion->name)) {
            $data = self::TOOL_QUESTION_ANSWER_REPLACEABLES[$toolQuestion->short];
            $toolQuestionForAnswer = ToolQuestion::findByShort($data['short']);

            $humanReadableAnswer = static::getHumanReadableAnswer(
                $building,
                ($toolQuestion->forSpecificInputSource ?? $inputSource),
                $toolQuestionForAnswer
            );

            $toolQuestion->name = __($toolQuestion->name, [$data['replaceable'] => $humanReadableAnswer]);
        }

        return $toolQuestion;
    }
}