<?php

namespace App\Http\Requests\Cooperation\Tool;

use App\Helpers\ConsiderableHelper;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class HighEfficiencyBoilerFormRequest extends FormRequest
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
        $max = Carbon::now()->year;

        return [
            'considerables.is_considering' => ['required', Rule::in(array_keys(ConsiderableHelper::getConsiderableValues()))],
            'user_energy_habits.amount_gas' => 'required|numeric|min:0|max:10000',
            'user_energy_habits.resident_count' => 'nullable|numeric|min:1|max:8',
            'building_services.service_value_id' => 'exists:service_values,id',
            'building_services.extra.date' => 'nullable|numeric|between:1970,'.$max,
        ];
    }
}
