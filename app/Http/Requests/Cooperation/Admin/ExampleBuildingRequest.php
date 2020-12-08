<?php

namespace App\Http\Requests\Cooperation\Admin;

use App\Helpers\Hoomdossier;
use App\Helpers\Old;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ExampleBuildingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Hoomdossier::user()->hasRoleAndIsCurrentRole(['super-admin', 'coordinator', 'cooperation-admin']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'content.*.content.roof-insulation.building_roof_types.*.roof_surface' => 'nullable|numeric',
            'content.*.content.roof-insulation.building_roof_types.*.insulation_roof_surface' => 'nullable|numeric',
            'building_type_id' => 'required|exists:building_types,id',
            'cooperation_id' => 'nullable|exists:cooperations,id',
            'is_default' => 'required|boolean',
            'order' => 'nullable|numeric|min:0',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function($validator) {
            $options = $this->input('content');
            $values = Arr::dot($options, 'content.');

            foreach ($values as $name => $value){
                if (Str::endsWith($name, ['surface', 'm2'])) {
                    // If surface is not null and surface is not numeric
                    if (!is_null($value) && !is_numeric($value)) {
                        $validator->errors()->add($name, 'Oppervlakte moet een nummer zijn (punt (.) gebruiken voor komma)');
                    }
                }
            }
        });
    }
}
