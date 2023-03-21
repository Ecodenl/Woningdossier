<?php

namespace App\Helpers;

use App\Models\MeasureApplication;
use App\Models\Step;
use App\Models\ToolLabel;
use App\Models\ToolQuestion;
use App\Services\DumpService;
use App\Services\RelatedModelService;

class ToolHelper
{
    const STRUCT_TOTAL = 'struct-total';
    const STRUCT_LITE = 'struct-lite';
    const STRUCT_SMALL_MEASURES_LITE = 'struct-small-measures-lite';
    const STRUCT_PDF_QUICK = 'struct-pdf-quick';
    const STRUCT_PDF_LITE = 'struct-pdf-lite';

    const STEP_STRUCTURE = [
        self::STRUCT_TOTAL => [
            // Quick Scan
            'building-data', 'usage-quick-scan', 'living-requirements', 'residential-status',
            // Expert Scan
            'ventilation', 'wall-insulation', 'insulated-glazing', 'floor-insulation', 'roof-insulation',
            'solar-panels',
            'heating' => [
                'huidige-situatie', 'nieuwe-situatie',
            ],
        ],
        self::STRUCT_LITE => [
            // Lite Scan
            'building-data-lite', 'usage-lite-scan', 'living-requirements-lite', 'residential-status-lite',
            'small-measures-lite',
        ],
        self::STRUCT_SMALL_MEASURES_LITE => [
            'small-measures-lite',
        ],
        // In a perfect world we could just use the total struct...
        self::STRUCT_PDF_QUICK => [
            // Quick Scan
            'building-data', 'usage-quick-scan', 'living-requirements', 'residential-status', 'small-measures',
            // Expert Scan
            'ventilation', 'wall-insulation', 'insulated-glazing', 'floor-insulation', 'roof-insulation',
            'solar-panels',
            'heating' => [
                'huidige-situatie', 'nieuwe-situatie',
            ],
        ],
        self::STRUCT_PDF_LITE => self::STRUCT_LITE,
    ];

    const SUPPORTED_RELATED_MODELS = [
        MeasureApplication::class,
    ];

    /**
     * Create the tool structure, which returns a mapping of shorts with labels attached.
     * These shorts could either be a save_in or a short to a model. If it's a model, a class
     * will be defined.
     *
     * @param string $short
     * @param string $mode
     *
     * @return array
     */
    public static function getContentStructure(string $short, string $mode): array
    {
        $stepOrder = static::getStepOrder($short);
        $relatedModelService = RelatedModelService::init();

        $structure = [];

        // Will hold shorts we already processed
        // Some models / morphs (mostly tool questions), will be shown on multiple steps
        // We dont want to do that for the CSV tho.
        // this way we can keep track of that
        $processedShorts = [];

        foreach ($stepOrder as $stepShortOrIndex => $stepDataOrStepShort) {
            $isSpecificOrder = false;
            $stepShort = $stepDataOrStepShort;

            if (is_string($stepShortOrIndex)) {
                // If it's a string, we have a step short as index, and the array holds the sub step order
                $stepShort = $stepShortOrIndex;
                $isSpecificOrder = true;
            }

            $step = Step::findByShort($stepShort);
            $query = $step->subSteps();
            if ($isSpecificOrder) {
                $locale = 'nl';

                // Order by custom order (it's important to ensure every sub step is in the array, else the order might be weird)
                $questionMarks = substr(str_repeat('?, ', count($stepDataOrStepShort)), 0, -2);
                $query->orderByRaw("FIELD(slug->>'$.{$locale}', {$questionMarks})", $stepDataOrStepShort);
            } else {
                $query->orderBy('order');
            }
            $subSteps = $query->get();

            foreach ($subSteps as $subStep) {
                $query = $subStep->subSteppables();
                if ($mode === DumpService::MODE_CSV) {
                    $query->whereNotIn('sub_steppable_type', [ToolLabel::class]);
                }
                $subSteppables = $query->orderBy('order')->get();
                foreach ($subSteppables as $subSteppable) {
                    $model = $subSteppable->subSteppable;

                    if (! array_key_exists($model->short, $processedShorts)
                        || ! in_array($subSteppable->sub_steppable_type, $processedShorts[$model->short])) {
                        $isToolQuestion = $subSteppable->sub_steppable_type === ToolQuestion::class;
                        $isToolLabel = $subSteppable->sub_steppable_type === ToolLabel::class;

                        // If it's a tool question we prefix with 'question_', if it's a tool label we
                        // prefix with 'label_' and otherwise it must be a calculation result so we
                        // prefix with 'calculation_'.
                        $prefix = $isToolQuestion ? 'question_'
                            : ($isToolLabel ? 'label_' : 'calculation_');

                        $shortToSave = $prefix . $model->short;

                        $modelName = $model->name;
                        if ($isToolQuestion && ! is_null($model->for_specific_input_source_id)) {
                            $modelName .= " ({$model->forSpecificInputSource->name})";
                        }

                        if ($mode === DumpService::MODE_CSV) {
                            if ($stepShort === 'heating' && ! $isToolQuestion && ! $isToolLabel) {
                                // Calculation fields have a repeated name, which can be confusing in only the heating
                                // step (as of now). Might need to be expanded later on. We add the tool label matched
                                // by the step short hidden in the result short
                                $labelShort = explode('.', $model->short)[0];
                                $label = ToolLabel::findByShort($labelShort);
                                $modelName .= " ({$label->name})";
                            } else {
                                $query = $relatedModelService->from($model)
                                    ->resolveTargetRaw()
                                    ->whereIn('target_model_type', static::SUPPORTED_RELATED_MODELS);

                                if ($query->exists()) {
                                    foreach ($query->get() as $mapping) {
                                        $relatedModel = $mapping->mappable;
                                        $relatedModelName = $relatedModel->name ?? $relatedModel->title;
                                        $modelName .= " ({$relatedModelName})";
                                    }
                                }
                            }
                        }

                        $structure[$stepShort][$shortToSave] = $modelName;

                        $processedShorts[$model->short][] = $subSteppable->sub_steppable_type;
                    }
                }
            }
        }

        return $structure;
    }

    private static function getStepOrder(string $short): array
    {
        do {
            // If the returned value is a string, then it's a short referencing a different step map. This is done
            // to preserve the amount of duplicate maps and keep things more readable.
            $stepOrder = array_key_exists($short, self::STEP_STRUCTURE) ? self::STEP_STRUCTURE[$short] : [];
            $short = is_string($stepOrder) ? $stepOrder : $short;
        } while (is_string($stepOrder));

        return $stepOrder;
    }
}
