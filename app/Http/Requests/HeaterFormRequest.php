<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class HeaterFormRequest extends FormRequest
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
        return [
            'building_heaters.pv_panel_orientation_id' => 'required|numeric|exists:pv_panel_orientations,id',
            'building_heaters.angle' => 'required|numeric|between:20,90',

            'user_energy_habits.water_comfort_id' => 'required|numeric|exists:comfort_level_tap_waters,id'
        ];
    }
}
