<?php

namespace App\Http\Requests\Cooperation\Tool;

use App\Http\Requests\DecimalReplacementTrait;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

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
        return \Auth::check();
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

        // Add the remaining rules
        return [
            // validate all the interested rules
            'user_interest.*.*' => 'required|exists:interests,id',



            // start


            // data about usage of the building
            'resident_count' => 'required|numeric|min:1|max:8',
            'cook_gas' => 'required|numeric',
            //'thermostat_high' => 'nullable|numeric|min:10|max:30|gte:thermostat_low',
            //'thermostat_low' => 'nullable|numeric|min:10|max:30|lte:thermostat_low',
            // Note the bail validator. We do this to prevent messages like
            // "Thermostat high must be between 8 and 30" or "Thermostat low must be between 10 and 100"
            // because the request variable is used for the between.
            // In a later Laravel version, the gte and lte validators can probably be used.
            'thermostat_high' => 'nullable|numeric|min:10|max:30|bail',
            'thermostat_low' => 'nullable|numeric|min:10|max:30|bail|between:10,' . max(10, $this->get('thermostat_high')),
            'heating_first_floor' => 'required|exists:building_heatings,id',
            'heating_second_floor' => 'required|exists:building_heatings,id',
            'water_comfort' => 'required|exists:comfort_level_tap_waters,id',
            'amount_electricity' => 'required|numeric|max:20000',
            'amount_gas' => 'required|numeric|min:0|max:10000',
            'motivation.*' => 'numeric',
        ];
    }
}
