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
                $serviceRules['service.' . $serviceId] = 'required|exists:services,id';
            } else {
                $serviceRules['service.' . $serviceId] = 'nullable|numeric';
            }
        }


        // Add the remaining rules
        $remainingRules = [
            // validate all the interested rules
            'user_interest.*.*' => 'required|exists:interests,id',

            // start
            'example_building_type' => 'required|exists:example_buildings,id',
            'building_type_id' => 'required|exists:building_types,id',
            'build_year' => 'required|numeric',
            'surface' => 'required|numeric',
            'monument' => 'numeric|digits_between:0,2',
	        'energy_label_id' => 'required|exists:energy_labels,id',
            'building_layers' => 'numeric|digits_between:1,999',
	        'roof_type_id' => 'required|exists:roof_types,id',

	        'element.*' => 'required|exists:element_values,id',
	        'user_interest_element.*' => 'required|exists:interests,id',
            // Energy measures
            //'windows_in_living_space' => 'required|exists:present_windows,id',
            //'windows_in_sleeping_spaces' => 'required|exists:present_windows,id',
            //'facade_insulation' => 'required|exists:qualities,id',
            //'floor_insulation' => 'required|exists:qualities,id',
            //'roof_insulation' => 'required|exists:qualities,id',
            'hr_cv_boiler' => 'exists:central_heating_ages,id',
            'hybrid_heatpump' => 'exists:present_heat_pumps,id',
            'monovalent_heatpump' => 'exists:present_heat_pumps,id',
            'sun_boiler' => 'exists:solar_water_heaters,id',
            'house_ventilation' => 'exists:ventilations,id',
            'house_ventilation_placed_date' => 'nullable|required_if:house_ventilation,2|date',
            'sun_panel' => 'nullable|numeric',
            'interested.sun_panel' => 'nullable|exists:interests,id',
            'sun_panel_placed_date' => 'nullable|date',

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

        $rules = array_merge($remainingRules);

        return $rules;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            foreach($this->request->get('service') as $serviceId => $serviceValueId) {

                $parentServiceField = 'service.' . $serviceId;

                // if the extra field has a value but the parent does not send them back
                if (Request::input($parentServiceField, '') <= 0 && Request::input($serviceId.'.extra', '') != "") {
                    $validator->errors()->add($serviceId.'.extra', __('auth.general-data.may-not-be-filled'));
                }
            }
        });
    }
}
