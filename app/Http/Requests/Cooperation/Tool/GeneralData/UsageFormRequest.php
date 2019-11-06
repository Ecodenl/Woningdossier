<?php

namespace App\Http\Requests\Cooperation\Tool\GeneralData;

use App\Http\Requests\DecimalReplacementTrait;
use Illuminate\Foundation\Http\FormRequest;

class UsageFormRequest extends FormRequest
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_energy_habits.resident_count' => 'required|numeric|min:1|max:8',
            'user_energy_habits.water_comfort_id' => 'required|exists:comfort_level_tap_waters,id',
            'user_energy_habits.cook_gas' => 'required|numeric',
            //'thermostat_high' => 'nullable|numeric|min:10|max:30|gte:thermostat_low',
            //'thermostat_low' => 'nullable|numeric|min:10|max:30|lte:thermostat_low',
            // Note the bail validator. We do this to prevent messages like
            // "Thermostat high must be between 8 and 30" or "Thermostat low must be between 10 and 100"
            // because the request variable is used for the between.
            // In a later Laravel version, the gte and lte validators can probably be used.
            'user_energy_habits.thermostat_high' => 'nullable|numeric|min:10|max:30|bail',
            'user_energy_habits.thermostat_low' => 'nullable|numeric|min:10|max:30|bail|between:10,' . max(10, $this->get('thermostat_high')),
            'user_energy_habits.hours_high' => 'required|numeric|between:1,24',
            'user_energy_habits.heating_first_floor' => 'required|exists:building_heatings,id',
            'user_energy_habits.heating_second_floor' => 'required|exists:building_heatings,id',
            'user_energy_habits.amount_electricity' => 'required|numeric|max:20000',
            'user_energy_habits.amount_gas' => 'required|numeric|min:0|max:10000',
        ];
    }
}
