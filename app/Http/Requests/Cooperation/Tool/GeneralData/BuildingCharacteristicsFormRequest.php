<?php

namespace App\Http\Requests\Cooperation\Tool\GeneralData;

use App\Http\Requests\DecimalReplacementTrait;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BuildingCharacteristicsFormRequest extends FormRequest
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
        $max = Carbon::now()->year;
        return [
            'building_features.*' => 'required',
            'building_features.surface' => 'numeric|min:20|max:600',
            'building_features.building_layers' => 'numeric|between:1,5',
            'building_features.build_year' => 'numeric|between:1000,'.$max,
            'building_features.monument' => 'nullable|numeric|digits_between:0,2',
            'building_features.roof_type_id' => [Rule::exists('roof_types', 'id')],
            'building_features.energy_label_id' => [Rule::exists('energy_labels','id')],
            'building_features.building_type_id' => [Rule::exists('building_types', 'id')],
            'buildings.example_building_id' => ['sometimes', 'nullable', Rule::exists('example_buildings', 'id')],
        ];
    }
}
