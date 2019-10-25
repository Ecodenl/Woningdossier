<?php

namespace App\Http\Requests\Cooperation\Tool;

use App\Models\Interest;
use App\Models\RoofType;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;

class RoofInsulationFormRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    protected function getRoofTypeCategory(RoofType $roofType)
    {
        if ($roofType->calculate_value <= 2) {
            return 'pitched';
        }
        if ($roofType->calculate_value <= 4) {
            return 'flat';
        }

        return '';
    }

    protected function getRoofTypeSubCategory(RoofType $roofType)
    {
        if (1 == $roofType->calculate_value) {
            return 'tiles';
        }
        if (2 == $roofType->calculate_value) {
            return 'bitumen';
        }
        if (4 == $roofType->calculate_value) {
            return 'zinc';
        }

        return '';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'building_roof_types.id' => 'bail|required|exists:roof_types,id',
            'building_roof_types.*.element_value_id' => 'exists:element_values,id',
            'building_roof_types.*.building_heating_id' => 'exists:building_heatings,id',
            'building_roof_types.*.extra.tiles_condition' => 'numeric|exists:roof_tile_statuses,id',
            'building_roof_types.*.extra.measure_application_id' => 'exists:measure_applications,id',
            'building_roof_types.roof_type_id' => 'exists:roof_types,id',
        ];
    }


    /**
     * small translation of the attribute.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'building_roof_types.id' => 'daktypes',
        ];
    }

    public function withValidator(Validator $validator)
    {

        $max = Carbon::now()->year;

        // retrieve the selected roof type ids
        $roofTypeIds = $this->input('building_roof_types.id');

        $brt = 'building_roof_types';
        // loop through them, and validate it
        foreach ($roofTypeIds as $roofTypeId) {
            $roofType = RoofType::find($roofTypeId);

            $roofTypeCategory = $this->getRoofTypeCategory($roofType);

            // when the roof type category exists add validation
            if (!empty($roofTypeCategory)) {
                $validator->addRules([
                    $brt.'.'.$roofTypeCategory.'.roof_surface' => 'required|numeric|min:0',
                    $brt.'.'.$roofTypeCategory.'.insulation_roof_surface' => 'nullable|min:0|needs_to_be_lower_or_same_as:'.$brt.'.'.$roofTypeCategory.'.roof_surface',
                    $brt.'.'.$roofTypeCategory.'.extra.zinc_replaced_date' => 'nullable|numeric|between:1960,'.$max,
                ]);


                // bitumen is only possible on a flat roof
                if ($roofTypeCategory === 'flat') {
                    $validator->addRules([
                        $brt.'.'.$roofTypeCategory.'.extra.bitumen_replaced_date' => 'nullable|numeric|between:1970,'.$max,
                    ]);
                }
            }
        }
    }
}
