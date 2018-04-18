<?php

namespace App\Http\Requests;

use App\Models\RoofType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    protected function getRoofTypeCategory(RoofType $roofType){
        if ($roofType->calculate_value <= 2){
            return 'pitched';
        }
        if ($roofType->calculate_value <= 4){
            return 'flat';
        }
        return '';
    }

    protected function getRoofTypeSubCategory(RoofType $roofType){
        if ($roofType->calculate_value == 1){
            return 'tiles';
        }
        if ($roofType->calculate_value == 2){
            return 'bitumen';
        }
        if ($roofType->calculate_value == 4){
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
        $roofTypes = $this->request->get('building_roof_types', []);

        $rules = [];
        $roofTypeRules = [];
        $roofTypeValueRules = [];

        foreach ($roofTypes as $i => $details) {
            // Validate the roof type values
            if (is_numeric($i) && is_numeric($details)) {
                $roofTypeRules['building_roof_types.'.$i] = 'exists:roof_types,id';

                $roofType = RoofType::find($details);

                $cat = $this->getRoofTypeCategory($roofType);
                // add as key to result array
                $result[$cat] = [
                    'type' => $this->getRoofTypeSubCategory($roofType),
                ];

                $roofTypeValueRules['building_roof_types.'.$cat.'.surface'] = 'number';
                $roofTypeValueRules['building_roof_types.'.$cat.'.element_value_id'] = 'exists:element_values,id';
                $roofTypeValueRules['building_roof_types.'.$cat.'.building_heating_id'] = 'exists:building_heatings,id';
                $roofTypeValueRules['building_roof_types.'.$cat.'.extra.bitumen_replaced_date'] = 'number';
                $roofTypeValueRules['building_roof_types.'.$cat.'.extra.zinc_replaced_date'] = 'number';
                $roofTypeValueRules['building_roof_types.'.$cat.'.extra.tiles_condition'] = 'number|exists:roof_tile_statuses,id';
                $roofTypeValueRules['building_roof_types.'.$cat.'.extra.measure_application_id'] = 'exists:measure_applications,id';
                $roofTypeValueRules['building_roof_types.roof_type_id'] = 'exists:roof_types,id';



                $rules = array_merge($roofTypeValueRules, $roofTypeRules);

            }
        }

        return [
            $rules
        ];
    }

    public function withValidator($validator)
    {
        // get the rooftypes
        $roofTypes = $this->request->get('building_roof_types', []);

        foreach ($roofTypes as $i => $details) {

            // Validate the roof type values
            if (is_numeric($i) && is_numeric($details)) {

                $roofType = RoofType::find($details);

                $cat = $this->getRoofTypeCategory($roofType);
                // add as key to result array
                $result[$cat] = [
                    'type' => $this->getRoofTypeSubCategory($roofType),
                ];



                // If the surface is empty but the roof type is set, throw a error
                $validator->after(function ($validator) use ($cat, $i, $result) {
                    if (Request::input('building_roof_types.'.$cat.'.surface') == "" && Request::input('building_roof_types.'.$i) != "") {
                        $validator->errors()->add('building_roof_types.'.$cat.'.surface', __('validation.custom.surface'));
                    }

                    // get the zinc category
                    $zincCat = isset($result['flat']['type']) ? $result['flat']['type'] : "";

                    if (Request::input('building_roof_types.'.$cat.'.extra.zinc_replaced_date') == "" && $zincCat == "zinc") {
                        $validator->errors()->add('building_roof_types.'.$cat.'.extra.zinc_replaced_date', __('validation.custom.surface'));
                    }

                    // if the roof is pitched and the replaced date is empty throw a error
                    //if (Request::input('building_roof_types.'.$cat.'.extra.bitumen_replaced_date') == "" && (isset($result['pitched'])) == true) {
                    //    $validator->errors()->add('building_roof_types.'.$cat.'.extra.bitumen_replaced_date', __('validation.custom.surface'));
                    //}
                });

            }
        }

    }
}
