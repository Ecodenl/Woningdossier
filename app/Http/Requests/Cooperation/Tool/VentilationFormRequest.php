<?php

namespace App\Http\Requests\Cooperation\Tool;

use App\Helpers\Cooperation\Tool\VentilationHelper;
use App\Helpers\HoomdossierSession;
use App\Models\ServiceValue;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Fluent;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class VentilationFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'building_ventilations.living_situation' => ['nullable', 'array', Rule::in(array_keys(VentilationHelper::getLivingSituationValues()))],
            'building_ventilations.usage' => ['nullable', 'array', Rule::in(array_keys(VentilationHelper::getUsageValues()))],
        ];
    }

    public function withValidator(Validator $validator)
    {

        $validator->sometimes('building_ventilations.how', ['required', 'array', Rule::in(array_keys(VentilationHelper::getHowValues()))], function (Fluent $input) {

            $building = HoomdossierSession::getBuilding(true);
            $buildingVentilationService = $building->getBuildingService('house-ventilation', HoomdossierSession::getInputSource(true));
            $buildingVentilation = $buildingVentilationService->serviceValue;

            // determine whether the field is required.
            // only when its 1 or 2 (Natuurlijk / Mechanisch) we have to show the how input
            // so in that kees its required.
            return in_array($buildingVentilation->calculate_value, [1,2,]);
        });
    }
}
