<?php

namespace App\Http\Requests\Cooperation\Tool;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class BuildingDetailRequest extends FormRequest
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
        $max = Carbon::now()->year;

        return [
            'building_type_id' => 'required|exists:building_types,id',
            'build_year' => 'required|numeric|between:1000,'.$max,
        ];
    }
}
