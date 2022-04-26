<?php

namespace App\Http\Requests\Cooperation\Tool;

use App\Helpers\ConsiderableHelper;
use App\Helpers\KeyFigures\PvPanels\KeyFigures;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SolarPanelFormRequest extends FormRequest
{
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
            'considerables.*.is_considering' => ['required', Rule::in(array_keys(ConsiderableHelper::getConsiderableValues()))],
            'building_pv_panels.peak_power' => ['required', 'numeric', Rule::in(KeyFigures::getPeakPowers())],
            'building_pv_panels.number' => 'required|numeric|min:0|max:50',
            'building_services.*.extra.value' => 'required|numeric|min:0|max:50',
            'building_pv_panels.angle' => 'required|numeric',
            'building_pv_panels.pv_panel_orientation_id' => 'required|exists:pv_panel_orientations,id',
            'building_pv_panels.total_installed_power' => 'nullable|numeric|max:18000|min:0',

            'user_energy_habits.amount_electricity' => 'required|numeric|max:25000',
        ];
    }
}
