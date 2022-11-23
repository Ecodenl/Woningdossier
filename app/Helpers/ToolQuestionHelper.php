<?php

namespace App\Helpers;

use App\Helpers\Conditions\ConditionEvaluator;
use App\Helpers\DataTypes\Caster;
use App\Helpers\QuestionValues\QuestionValue;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\ToolQuestion;
use Illuminate\Support\Facades\Schema;

class ToolQuestionHelper
{
    /**
     * The tool questions (shorts) that are allowed to be filled upon register.
     * Note: When changing this, ensure you update the Swagger Docs in the Register Controller and the related tests!
     * @var array
     */
    const SUPPORTED_API_SHORTS = [
        'amount-gas',
        'amount-electricity',
        'resident-count',
    ];

    /**
     * These tables should query on one or more extra column(s)
     * Order for multiple columns is very important, it should be ordered as the where values in the save_in
     */
    const TABLE_COLUMN = [
        'building_elements' => ['element_id'],
        'building_insulated_glazings' => ['measure_application_id'],
        'building_roof_types' => ['roof_type_id'],
        'building_services' => ['service_id'],
        'considerables' => ['considerable_type', 'considerable_id'],
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
        'water-comfort' => ['high-efficiency-boiler', 'heater', 'heat-pump'],
        'cook-type' => ['high-efficiency-boiler', 'heater', 'heat-pump'],
        'current-wall-insulation' => ['wall-insulation'],
        'current-floor-insulation' => ['floor-insulation'],
        'current-roof-insulation' => ['roof-insulation'],
        'current-living-rooms-windows' => ['insulated-glazing'],
        'current-sleeping-rooms-windows' => ['insulated-glazing'],
        'heat-source' => ['high-efficiency-boiler', 'heater', 'heat-pump'],
        'heat-source-warm-tap-water' => ['high-efficiency-boiler', 'heater', 'heat-pump'],
        'boiler-type' => ['high-efficiency-boiler', 'heater', 'heat-pump'],
        'boiler-placed-date' => ['high-efficiency-boiler', 'heater', 'heat-pump'],
        'heat-pump-type' => ['high-efficiency-boiler', 'heat-pump', 'heater',],
        'heat-pump-placed-date' => ['high-efficiency-boiler', 'heat-pump', 'heater',],
        'interested-in-heat-pump' => ['high-efficiency-boiler', 'heat-pump', 'heater',],
        'interested-in-heat-pump-variant' => ['high-efficiency-boiler', 'heat-pump', 'heater',],
        'ventilation-type' => ['ventilation'],
        'ventilation-demand-driven' => ['ventilation'],
        'ventilation-heat-recovery' => ['ventilation'],
        'crack-sealing-type' => ['ventilation'],
        'has-solar-panels' => ['solar-panels'],
        'solar-panel-count' => ['solar-panels'],
        'total-installed-power' => ['solar-panels'],
        'solar-panels-placed-date' => ['solar-panels'],
        'heater-pv-panel-orientation' => ['high-efficiency-boiler', 'heater', 'heat-pump'],
        'heater-pv-panel-angle' => ['high-efficiency-boiler', 'heater', 'heat-pump'],
        'fifty-degree-test' => ['high-efficiency-boiler', 'heater', 'heat-pump'],
        'boiler-setting-comfort-heat' => ['high-efficiency-boiler', 'heater', 'heat-pump'],
        'new-boiler-setting-comfort-heat' => ['high-efficiency-boiler', 'heater', 'heat-pump'],
        'new-heat-source' => ['high-efficiency-boiler', 'heater', 'heat-pump'],
        'new-heat-source-warm-tap-water' => ['high-efficiency-boiler', 'heater', 'heat-pump'],
        'hr-boiler-replace' => ['high-efficiency-boiler', 'heater', 'heat-pump'],
        'new-boiler-type' => ['high-efficiency-boiler', 'heater', 'heat-pump'],
        'heat-pump-replace' => ['high-efficiency-boiler', 'heat-pump', 'heater',],
        'new-heat-pump-type' => ['high-efficiency-boiler', 'heat-pump', 'heater',],
        'heat-pump-preferred-power' => ['high-efficiency-boiler', 'heat-pump', 'heater',],
        'outside-unit-space' => ['high-efficiency-boiler', 'heat-pump', 'heater',],
        'inside-unit-space' => ['high-efficiency-boiler', 'heat-pump', 'heater',],
        'heat-pump-boiler-replace' => ['high-efficiency-boiler', 'heat-pump', 'heater',],
        'sun-boiler-replace' => ['high-efficiency-boiler', 'heater', 'heat-pump'],
        'new-water-comfort' => ['high-efficiency-boiler', 'heater', 'heat-pump'],
    ];

    /**
     * Who would be surprised that some questions should trigger a recalculate but only in certain conditions?
     */
    const TOOL_RECALCULATE_CONDITIONS = [
        'interested-in-heat-pump' => [
            [
                [
                    'column' => 'fn',
                    'operator' => 'HasCompletedStep',
                    'value' => [
                        'steps' => ['heating'],
                        'input_source_shorts' => [
                            InputSource::RESIDENT_SHORT,
                            InputSource::COACH_SHORT,
                        ],
                        'should_pass' => false,
                    ],
                ],
            ],
        ],
        'interested-in-heat-pump-variant' => [
            [
                [
                    'column' => 'fn',
                    'operator' => 'HasCompletedStep',
                    'value' => [
                        'steps' => ['heating'],
                        'input_source_shorts' => [
                            InputSource::RESIDENT_SHORT,
                            InputSource::COACH_SHORT,
                        ],
                        'should_pass' => false,
                    ],
                ],
            ],
        ],
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

    public static function stepShortsForToolQuestion(ToolQuestion $toolQuestion, Building $building, InputSource $inputSource): array
    {
        if (isset(self::TOOL_QUESTION_STEP_MAP[$toolQuestion->short])) {
            $data = self::TOOL_QUESTION_STEP_MAP[$toolQuestion->short];

            // Only return if it passes the conditions (if there are any)
            if (array_key_exists($toolQuestion->short, self::TOOL_RECALCULATE_CONDITIONS)) {
                $pass = ConditionEvaluator::init()->building($building)->inputSource($inputSource)
                    ->evaluate(self::TOOL_RECALCULATE_CONDITIONS[$toolQuestion->short]);

                $data = $pass ? $data : [];
            }

            return $data;
        }
        return [];
    }

    /**
     * Simple method to determine whether the given tool question should use the old advice on a recalculate
     *
     */
    public static function shouldToolQuestionDoFullRecalculate(ToolQuestion $toolQuestion, Building $building, InputSource $inputSource): bool
    {
        if (in_array($toolQuestion->short, self::TOOL_QUESTION_FULL_RECALCULATE, true)) {
            $pass = true;

            // Only return if it passes the conditions (if there are any)
            if (array_key_exists($toolQuestion->short, self::TOOL_RECALCULATE_CONDITIONS)) {
                $pass = ConditionEvaluator::init()->building($building)->inputSource($inputSource)
                    ->evaluate(self::TOOL_RECALCULATE_CONDITIONS[$toolQuestion->short]);
            }

            return $pass;
        }

        return false;
    }

    /**
     * Simple method to resolve the save in to something we can use.
     *
     * @param string $saveIn
     * @param Building $building
     *
     * @return array
     */
    public static function resolveSaveIn(string $saveIn, Building $building): array
    {
        $savedInParts = explode('.', $saveIn);
        $table = array_shift($savedInParts);
        $column = array_pop($savedInParts);
        $where = [];

        if (Schema::hasColumn($table, 'user_id')) {
            $where['user_id'] = $building->user_id;
        } else {
            $where['building_id'] = $building->id;
        }

        // if there are saved in parts left, check if we should add extra wheres or prepend it to the column.
        if (count($savedInParts) > 0) {
            // first check if the table has additional wheres
            if (isset(ToolQuestionHelper::TABLE_COLUMN[$table])) {
                // it does, check which are wheres and which are a columns
                $columns = ToolQuestionHelper::TABLE_COLUMN[$table];

                foreach ($savedInParts as $index => $value) {
                    if (isset($columns[$index])) {
                        $where[$columns[$index]] = $value;
                    } else {
                        $column = $value . '.' . $column;
                    }
                }
            } else {
                // so no columns for the table are found, al of the extra saved in parts are columns.
                $column = implode('.', $savedInParts).'.'.$column;
            }
        }

        return compact('table', 'column', 'where');
    }

    /**
     * Get a human readable answer.
     *
     * @param \App\Models\Building $building
     * @param \App\Models\InputSource $inputSource
     * @param \App\Models\ToolQuestion $toolQuestion
     * @param bool $withIcons
     * @param null $answer
     *
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|int|mixed|string|string[]|null
     */
    public static function getHumanReadableAnswer(Building $building, InputSource $inputSource, ToolQuestion $toolQuestion, bool $withIcons = false, $answer = null)
    {
        $humanReadableAnswer = __('cooperation/frontend/tool.no-answer-given');

        if (empty($answer)) {
            $answer = $building->getAnswer($inputSource, $toolQuestion);
        }

        if (! empty($answer) || (is_numeric($answer) && (int) $answer === 0)) {
            $questionValues = QuestionValue::init($building->user->cooperation, $toolQuestion)
                ->forInputSource($inputSource)
                ->forBuilding($building)
                ->withCustomEvaluation()
                ->getQuestionValues();

            if ($questionValues->isNotEmpty()) {
                $humanReadableAnswers = [];

                $answer = is_array($answer) ? $answer : [$answer];

                foreach ($answer as $subAnswer) {
                    $questionValue = $questionValues->where('value', '=', $subAnswer)->first();

                    if (! empty($questionValue)) {
                        $answerToAppend = strip_tags($questionValue['name']);

                        if (! empty($questionValue['extra']['icon']) && $withIcons) {
                            $answerToAppend .= '<i class="ml-1 w-8 h-8 ' . $questionValue['extra']['icon'] . '"></i>';
                        }

                        $humanReadableAnswers[] = $answerToAppend;
                    }
                }

                return implode(', ', $humanReadableAnswers);
            } else {
                // If there are no question values, then it's user input
                $humanReadableAnswer = $answer;

                if ($toolQuestion->data_type == Caster::STRING ) {
                    $humanReadableAnswer = strip_tags($answer);
                }
            }

            // Format answers
            if (in_array($toolQuestion->data_type, [Caster::INT, Caster::FLOAT])) {
                $humanReadableAnswer = Caster::init($toolQuestion->data_type, $humanReadableAnswer)->getFormatForUser();
            } elseif ($toolQuestion->data_type === Caster::JSON) {
                $humanReadableAnswerArray = Caster::init($toolQuestion->data_type, $humanReadableAnswer)->getCast();
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
     * @param \App\Models\Building $building
     * @param \App\Models\InputSource $inputSource
     * @param \App\Models\ToolQuestion $toolQuestion
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