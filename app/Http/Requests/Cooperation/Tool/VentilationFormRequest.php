<?php

namespace App\Http\Requests\Cooperation\Tool;

use App\Helpers\Cooperation\Tool\VentilationHelper;
use App\Helpers\HoomdossierSession;
use App\Models\InputSource;
use App\Models\Step;
use App\Models\ToolQuestion;
use App\Services\LegacyService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Fluent;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class VentilationFormRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(LegacyService $legacyService): array
    {
        $measureRelatedShorts = $legacyService->getToolQuestionShorts(Step::findByShort('ventilation'));

        $rules = [
            'building_ventilations.living_situation' => ['nullable', 'array', Rule::in(array_keys(VentilationHelper::getLivingSituationValues()))],
            'building_ventilations.usage' => ['nullable', 'array', Rule::in(array_keys(VentilationHelper::getUsageValues()))],
        ];

        foreach ($measureRelatedShorts as $tqShorts) {
            $toolQuestions = ToolQuestion::findByShorts($tqShorts);
            foreach ($toolQuestions as $toolQuestion) {
                $rules[$toolQuestion->short] = $toolQuestion->validation;
            }
        }

        return $rules;
    }

    public function withValidator(Validator $validator)
    {
        $validator->sometimes('building_ventilations.how', ['required', 'array', Rule::in(array_keys(VentilationHelper::getHowValues()))], function (Fluent $input) {
            $building = HoomdossierSession::getBuilding(true);
            $buildingVentilationService = $building->getBuildingService('house-ventilation', InputSource::findByShort(InputSource::MASTER_SHORT));
            $buildingVentilation = $buildingVentilationService->serviceValue;

            // determine whether the field is required.
            // only when its 1 or 2 (Natuurlijk / Mechanisch) we have to show the how input
            // so in that kees its required.
            return in_array($buildingVentilation->calculate_value, [1, 2]);
        });
    }
}
