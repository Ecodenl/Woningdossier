<?php

namespace App\Http\Requests\Cooperation\Tool;

use App\Http\Requests\DecimalReplacementTrait;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;

class InsulatedGlazingFormRequest extends FormRequest
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

    public function prepareForValidation()
    {
        $this->decimals([
            'building_insulated_glazings' => 'm2',
            'window_surface',
        ]);
    }

    public function rules()
    {
        $max = Carbon::now()->year;

        // m2 and window rules in the withValidator
        $rules = [
            'building_elements.*' => 'required|exists:element_values,id',
            'building_elements.*.*' => 'exists:element_values,id',
            'building_features.window_surface' => 'nullable|numeric|min:1',
            'building_paintwork_statuses.wood_rot_status_id' => 'required|exists:wood_rot_statuses,id',
            'building_paintwork_statuses.paintwork_status_id' => 'required|exists:paintwork_statuses,id',
            'building_paintwork_statuses.last_painted_year' => 'nullable|numeric|between:1990,'.$max,
        ];

        return $rules;
    }

    public function withValidator(Validator $validator)
    {
        $big = 'building_insulated_glazings.';

        foreach ($this->get('building_insulated_glazings') as $measureApplicationId => $bigData) {
            $validator->addRules([
                $big.$measureApplicationId.'.m2' => 'nullable|numeric|min:1',
                $big.$measureApplicationId.'.windows' => 'nullable|numeric|min:1',
            ]);
        }
    }
}
