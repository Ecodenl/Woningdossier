<?php

namespace App\Http\Requests\Cooperation\Tool;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

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
            'habit.gas_usage' => 'required|numeric|min:0|max:10000',
            'habit.resident_count' => 'nullable|numeric|min:1|max:10',
//            'habit.*' => 'required|numeric',
            'building_services.*.service_value_id' => 'exists:service_values,id',
            'building_services.*.extra' => 'nullable|numeric|between:1970,'.$max,
        ];
    }
}
