<?php

namespace App\Http\Requests;

use App\Models\Service;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GeneralDataFormRequest extends FormRequest
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {

        foreach($this->request->get('service') as $serviceId => $serviceValueId) {
            $service = Service::find($serviceId);
            // if the service exist it has service values, check if it exist
            if ($service->values()->where('service_id', $serviceId)->first() != null) {
                $serviceRules['service.' . $serviceId] = 'required|exists:service_values,id';
            } else {
                $serviceRules['service.' . $serviceId] = 'nullable|numeric';
            }

            if ($service->short == "house-ventilation" || $service->short == "total-sun-panels") {
                // The extra field for the service field
                $serviceRules[$serviceId.'.extra'] = 'nullable|date';
            }


        }


        // Add the remaining rules
        $remainingRules = [
            // validate all the interested rules
            'user_interest.*.*' => 'required|exists:interests,id',

            // Validate the elements
            'element.*' => 'required|exists:element_values,id',

            // start
            'example_building_type' => 'required|exists:example_buildings,id',
            'building_type_id' => 'required|exists:building_types,id',
            'build_year' => 'required|numeric',
            'surface' => 'required|numeric',
            'monument' => 'numeric|digits_between:0,2',
	        'energy_label_id' => 'required|exists:energy_labels,id',
            'building_layers' => 'numeric|digits_between:1,999',
	        'roof_type_id' => 'required|exists:roof_types,id',

            // data about usage of the building
            'resident_count' => 'required|numeric',
            'cook_gas' => 'required|numeric',
            'thermostat_high' => 'nullable|numeric',
            'thermostat_low' => 'nullable|numeric|digits_between:0,'.$this->request->get('thermostat_high'),
            'heating_first_floor' => 'required|numeric|exists:building_heatings,id',
            'heating_second_floor' => 'required|numeric|exists:building_heatings,id',
            'water_comfort' => 'numeric|exists:comfort_level_tap_waters,id',
            'amount_electricity' => 'nullable|numeric',
            'amount_gas' => 'nullable|numeric',
            'motivation.*' => 'numeric'
        ];

        $rules = array_merge($remainingRules, $serviceRules);

        return $rules;
    }

    public function withValidator($validator)
    {
        // Maybe take a look when its needed
//        $validator->after(function ($validator) {
//            foreach($this->request->get('service') as $serviceId => $serviceValueId) {
//
//                $parentServiceField = 'service.' . $serviceId;
//
//                // Find a service
//                $service = Service::find($serviceId);
//
//                // if the extra field has a value but the parent does not send them back
//                if (Request::input($parentServiceField, '') <= 0 && Request::input($serviceId.'.extra', '') != "") {
//                    $validator->errors()->add($serviceId.'.extra', __('auth.general-data.may-not-be-filled'));
//                }
//                // This will check if the ventilation field and the date field are valid
//                // If the ventilation field value is not mechanic and the extra / date field is not empty return an error
//                if($service->short == "house-ventilation") {
//
//                    // The selected service, calculate value
//                    $currentServiceCalculateValue = $service->values()->find($serviceValueId)->calculate_value;
//                    // The extra field for the service field
//                    $serviceExtra = Request::input(''.$serviceId.'.extra', '');
//
//                    if ($currentServiceCalculateValue == 2  || $currentServiceCalculateValue == 4 && $serviceExtra != "") {
//                        // if the selected calculate value = 2 or 4 do nothing
//                    } else  {
//                        // throw error
//                        $validator->errors()->add(''.$serviceId.'.extra', __('auth.general-data.may-not-be-filled'));
//                    }
//                }
//            }
//        });
    }
}
