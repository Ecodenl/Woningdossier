<?php

namespace App\Services\Econobis\Payloads;

use App\Helpers\Calculator;
use App\Models\InputSource;
use App\Models\RoofType;
use App\Models\ToolQuestion;
use App\Models\UserActionPlanAdvice;
use App\Services\UserActionPlanAdviceService;

class WoonplanPayload extends EconobisPayload
{
    public function buildPayload(): array
    {
        // the key = tool question
        // the values are measure applications that are "related" to the question
        // they are key to calculate the measure
        $toolQuestionRelatedMeasureMap = [
            'insulation-floor-surface' => ['floor-insulation', 'bottom-insulation', 'floor-insulation-research'],
            'insulation-wall-surface' => [
                'cavity-wall-insulation' => '',
                'facade-wall-insulation' => 'insulation-wall-surface',
                'wall-insulation-research' => 'insulation-wall-surface',
            ],
            'glass-in-lead-replace-glass-surface' => ['glass-in-lead'],
            'hrpp-glass-only-replacement-glass-surface' => ['hrpp-glass-only'],
            'hrpp-glass-frame-replacement-glass-surface' => ['hrpp-glass-frames'],
            'hr3p-glass-frame-replacement-glass-surface' => ['hr3p-frames'],

            'pitched-roof-insulation-surface' => [
                'roof-insulation-pitched-inside',
                'roof-insulation-pitched-replace-tiles'
            ],
            'roof-flat-roof-insulation-surface' => [
                'roof-insulation-flat-current' => '',
                'roof-insulation-flat-replace-current' => 'roof-flat-roof-insulation-surface',
            ],
            'desired-solar-panel-count' => ['solar-panels-place-replace'],

        ];
        $building = $this->building;

        $inputSource = InputSource::findByShort(InputSource::MASTER_SHORT);

        $advices = $building
            ->user
            ->userActionPlanAdvices()
            ->forInputSource($inputSource)
            ->category(UserActionPlanAdviceService::CATEGORY_TO_DO)
            ->get();

        /** @var UserActionPlanAdvice $advice */
        foreach ($advices as $advice) {
            $advisable = $advice->userActionPlanAdvisable()->forInputSource($inputSource)->first();
            // the simple case.
//            foreach ($stepInsulationSurfaceMap)
//            $insulationSurface = $this->building->getAnswer($inputSource, ToolQuestion::findByShort($insulationSurfaceToolQuestionShort));



            $data['user_action_plan_advices'][] = [
                'name' => $advisable->measure_name ?? $advisable->name,
                'savings_gas' => $advice->savings_gas,
                'savings_electricity' => $advice->savings_electricity,
                'co2_savings' => Calculator::calculateCo2Savings($advice->savings_gas),
                'measure_id' => $advice->user_action_plan_advisable_id,
                'measure_type' => $advice->user_action_plan_advisable_type,
//                'insulation_surface' => $insulationSurface
                // 'surface' => ''
            ];
        }

        dd($data);
        return $data;
    }
}