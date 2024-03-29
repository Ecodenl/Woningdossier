<?php

namespace App\Services\Econobis\Payloads;

use App\Models\ToolQuestion;
use Illuminate\Support\Arr;

class GebruikPayload extends EconobisPayload
{
    use MasterInputSource;

    public function buildPayload(): array
    {

        $applicableToolQuestions = [
            'current' => [
                "building-type-category",
                "building-type",
                "building-contract-type",
                "build-year",
                "building-layers",
                "roof-type",
                "monument",
                "energy-label",
                "surface",
                "resident-count",
                "water-comfort",
                "cook-type",
                "amount-gas",
                "amount-electricity",
                "current-wall-insulation",
                "current-floor-insulation",
                "current-roof-insulation",
                "current-living-rooms-windows",
                "current-sleeping-rooms-windows",
                "heat-source",
                "heat-source-other",
                "heat-source-warm-tap-water",
                "heat-source-warm-tap-water-other",
                "boiler-type",
                "boiler-placed-date",
                "heat-pump-type",
                "heat-pump-placed-date",
                "building-heating-application",
                "building-heating-application-other",
                "boiler-setting-comfort-heat",
                "ventilation-type",
                "ventilation-demand-driven",
                "ventilation-heat-recovery",
                "crack-sealing-type",

                "ventilation-type",
                "has-cavity-wall",
                "wall-surface",
                "apply-led-light-how",
                "total-window-surface",
                "frame-type",
                "floor-surface",
                "apply-led-light-how",
                "current-roof-types",
                "is-pitched-roof-insulated",
                "pitched-roof-surface",
                "pitched-roof-insulation",
                "pitched-roof-heating",
                "is-flat-roof-insulated",
                "flat-roof-surface",
                "flat-roof-insulation",
                "flat-roof-heating",

                "has-solar-panels",
                "solar-panel-count",
                "total-installed-power",
                "solar-panels-placed-date",

                "apply-led-light-how",
                "heat-pump-preferred-power",
                "outside-unit-space",
                "inside-unit-space",
                "heater-pv-panel-orientation",
                "heater-pv-panel-angle",
                "hrpp-glass-only-current-glass",
                "hrpp-glass-only-replacement-glass-surface",
                "hrpp-glass-only-replacement-glass-count",
                "hrpp-glass-frame-current-glass",
                "hrpp-glass-frame-replacement-glass-surface",
                "hrpp-glass-frame-replacement-glass-count",
                "hr3p-glass-frame-current-glass",
                "hr3p-glass-frame-rooms-heated",
                "hr3p-glass-frame-replacement-glass-surface",
                "hr3p-glass-frame-replacement-glass-count",
                "glass-in-lead-replace-current-glass",
                "glass-in-lead-replace-rooms-heated",
                "glass-in-lead-replace-glass-surface",
                "glass-in-lead-replace-glass-count"
            ],
            'new' => [
                "new-water-comfort",
                "new-heat-source",
                "new-heat-source-warm-tap-water",
                "new-building-heating-application",
                "new-boiler-type",
                "new-boiler-setting-comfort-heat",
                "new-cook-type",
                "new-heat-pump-type",

                'insulation-wall-surface',
                "flat-roof-insulation-surface",
                "pitched-roof-insulation-surface",

                "solar-panel-peak-power",
                "desired-solar-panel-count",
                "solar-panel-orientation",
                "solar-panel-angle",
            ],
        ];

        foreach ($applicableToolQuestions as $situation => $toolQuestionShorts) {
            foreach ($toolQuestionShorts as $index => $toolQuestionShort) {
                $answers = [];
                $toolQuestion = ToolQuestion::findByShort($toolQuestionShort);

                $values = $toolQuestion->getQuestionValues();

                $buildingAnswers = $this->building->getAnswer($this->masterInputSource, $toolQuestion);

                $applicableToolQuestions[$situation][$toolQuestionShort] = [
                    'id' => $toolQuestion->id,
                    'short' => $toolQuestion->short,
                    'name' => $toolQuestion->name,
                ];

                if ($values->isNotEmpty()) {
                    if (is_array($buildingAnswers)) {
                        foreach ($buildingAnswers as $buildingAnswer) {
                            $buildingAnswerRelatedData = $values->where('value', $buildingAnswer)->first();
                            $answers[] = Arr::only($buildingAnswerRelatedData, ['value', 'name', 'short']);
                        }
                    } else {
                        if ( ! is_null($buildingAnswers)) {
                            $buildingAnswerRelatedData = $values->where('value', $buildingAnswers)->first();
                            $answers = Arr::only($buildingAnswerRelatedData, ['value', 'name', 'short']);
                            if ( ! isset($answers['short'])) {
                                $answers['short'] = null;
                            }
                        }
                    }
                } else {
                    if ( ! is_null($buildingAnswers)) {
                        $answers = [
                            'value' => $buildingAnswers,
                            'short' => null,
                            'name' => null
                        ];
                    }
                }

                $applicableToolQuestions[$situation][$toolQuestionShort]['answers'] = $answers;

                unset($applicableToolQuestions[$situation][$index]);
            }
        }

        return $applicableToolQuestions;
    }
}