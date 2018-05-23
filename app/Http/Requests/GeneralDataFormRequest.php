<?php

namespace App\Http\Requests;

use App\Models\Service;
use App\Models\Motivation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GeneralDataFormRequest extends FormRequest
{

	use DecimalReplacementTrait;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

	public function getValidatorInstance()
	{
		$this->decimals(['surface', 'thermostat_high', 'thermostat_low']);

		return parent::getValidatorInstance();
	}

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        foreach($this->request->get('service') as $serviceId => $serviceValueId) {
        	if (!is_null($serviceValueId)) {
		        $service = Service::find( $serviceId );
		        // if the service exists it has service values, check if it exists
		        if ( $service->values()->where( 'service_id',
				        $serviceId )->first() != null ) {
			        $serviceRules[ 'service.' . $serviceId ] = 'required|exists:service_values,id';
		        } else {
			        $serviceRules[ 'service.' . $serviceId ] = 'nullable|numeric';
		        }

		        if ( $service->short == "house-ventilation") {
			        // The extra field for the service field
			        $serviceRules[ $serviceId . '.extra.year' ] = 'nullable|numeric';
		        }
		        if ( $service->short == "total-sun-panels" ) {
			        // The extra field for the service field
			        //$serviceRules[ $serviceId . '.extra.value' ] = 'nullable|numeric';
			        $serviceRules[ $serviceId . '.extra.year' ] = 'nullable|numeric';
		        }
	        }
        }

        // Add the remaining rules
        $remainingRules = [
            // validate all the interested rules
            'user_interest.*.*' => 'required|exists:interests,id',

            // Validate the elements
            'element.*' => 'required|exists:element_values,id',

            // start
            'example_building_id' => 'nullable|exists:example_buildings,id',
            'building_type_id' => 'required|exists:building_types,id',
            'build_year' => 'required|numeric',
            'surface' => 'required|numeric',
            'monument' => 'numeric|digits_between:0,2',
	        'energy_label_id' => 'required|exists:energy_labels,id',
            'building_layers' => 'numeric|digits_between:1,5',
	        'roof_type_id' => 'required|exists:roof_types,id',

            // data about usage of the building
            'resident_count' => 'required|numeric',
            'cook_gas' => 'required|numeric',
            'thermostat_high' => 'nullable|numeric',
            'thermostat_low' => 'nullable|numeric|between:0,'.$this->request->get('thermostat_high'),
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

}
