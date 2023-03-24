<?php

namespace App\Services\Econobis\Payloads;

use App\Helpers\Calculator;
use App\Models\CooperationMeasureApplication;
use App\Models\CustomMeasureApplication;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\MeasureCategory;
use App\Models\RelatedModel;
use App\Models\RoofType;
use App\Models\ToolQuestion;
use App\Models\UserActionPlanAdvice;
use App\Services\DiscordNotifier;
use App\Services\MappingService;
use App\Services\RelatedModelService;
use App\Services\UserActionPlanAdviceService;

class WoonplanPayload extends EconobisPayload
{
    use MasterInputSource;

    public function buildPayload(): array
    {
        $payload = [];
        // the key = tool question
        // the values are measure applications that are "related" to the question
        // they are key to calculate the measure
        $toolQuestionRelatedMeasureMap = [
            'insulation-floor-surface' => [
                'type' => 'surface',
                'measures' => [
                    'floor-insulation',
                    'bottom-insulation',
                    'floor-insulation-research'
                ]
            ],
            'insulation-wall-surface' => [
                'type' => 'surface',
                'measures' => [
                    'cavity-wall-insulation',
                    'facade-wall-insulation',
                    'wall-insulation-research',
                ],
            ],
            'glass-in-lead-replace-glass-surface' => [
                'type' => 'surface',
                'measures' => ['glass-in-lead']
            ],
            'hrpp-glass-only-replacement-glass-surface' => [
                'type' => 'surface',
                'measures' => ['hrpp-glass-only']
            ],
            'hrpp-glass-frame-replacement-glass-surface' => [
                'type' => 'surface',
                'measures' => ['hrpp-glass-frames']
            ],
            'hr3p-glass-frame-replacement-glass-surface' => [
                'type' => 'surface',
                'measures' => ['hr3p-frames']
            ],

            'pitched-roof-insulation-surface' => [
                'type' => 'surface',
                'measures' => [
                    'roof-insulation-pitched-inside',
                    'roof-insulation-pitched-replace-tiles'
                ],
            ],
            'roof-flat-roof-insulation-surface' => [
                'type' => 'surface',
                'measures' => [
                    'roof-insulation-flat-current',
                    'roof-insulation-flat-replace-current',
                ]
            ],
            'desired-solar-panel-count' => [
                'type' => 'count',
                'measures' => [
                    'solar-panels-place-replace'
                ],
            ]
        ];
        $building = $this->building;

        $advices = $building
            ->user
            ->userActionPlanAdvices()
            ->forInputSource($this->masterInputSource)
            ->category(UserActionPlanAdviceService::CATEGORY_TO_DO)
            ->get();

        /** @var UserActionPlanAdvice $advice */
        foreach ($advices as $advice) {
            $advisable = $advice->userActionPlanAdvisable()->forInputSource($this->masterInputSource)->first();
            // the simple case.
            $relatedToolQuestion = null;
            foreach ($toolQuestionRelatedMeasureMap as $toolQuestionShort => $mapInfo) {
                if (in_array($advisable->short, $mapInfo['measures'])) {
                    $relatedToolQuestion = ToolQuestion::findByShort($toolQuestionShort);
                    break;
                }
            }

            // the default payload for each and every advisable
            $payload['user_action_plan_advices'][$advice->id] = [
                'name' => $advisable->measure_name ?? $advisable->name,
                'savings_gas' => $advice->savings_gas,
                'savings_electricity' => $advice->savings_electricity,
                'co2_savings' => Calculator::calculateCo2Savings($advice->savings_gas),
                'measure_id' => $advice->user_action_plan_advisable_id,
                'measure_type' => $advice->user_action_plan_advisable_type,
            ];

            if ($advisable instanceof CustomMeasureApplication || $advisable instanceof CooperationMeasureApplication) {
                $measureCategory =  app(MappingService::class)
                    ->from($advisable)
                    ->resolveTarget()
                    ->first();

                if ($measureCategory instanceof MeasureCategory) {
                    $payload['user_action_plan_advices'][$advice->id]['measure_category'] = $measureCategory->only(['id', 'short', 'name']);
                } else {
                    unset($payload['user_action_plan_advices'][$advice->id]);
                }
            }

            if ($relatedToolQuestion instanceof ToolQuestion && $advisable instanceof MeasureApplication) {
                $relatedAnswer = $this->building->getAnswer($this->masterInputSource, $relatedToolQuestion);
                $type = $toolQuestionRelatedMeasureMap[$relatedToolQuestion->short]['type'];
                $whereTarget = app(RelatedModelService::class)->target($advisable)->whereTarget();

                $executeHowToolQuestion = RelatedModel::with(['resolvable'])
                    ->whereHas('resolvable', function ($query) {
                        // so ofcourse this isnt exactly the most solid way
                        // maybe we cancollect al the how shorts omewhere and do a wherein
                        // as we already query PER measure application
                        // "Maar dat is voor morgen" ~ John F. Kennedy
                        $query->where('short', 'LIKE', "%how%");

                    })->where($whereTarget)->first()->from_model;

                $answer = $building->getAnswer($this->masterInputSource, $executeHowToolQuestion);

                $payload['user_action_plan_advices'][$advice->id][$type] = $relatedAnswer;
                // if the user doesnt want to let-do, he wants to execute himself.
                $payload['user_action_plan_advices'][$advice->id]['execute_self'] = $answer !== 'let-do';
            }
        }

        return $payload;
    }
}