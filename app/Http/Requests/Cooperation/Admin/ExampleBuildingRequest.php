<?php

namespace App\Http\Requests\Cooperation\Admin;

use App\Helpers\Old;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class ExampleBuildingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \Auth::account()->user()->hasRoleAndIsCurrentRole(['super-admin', 'coordinator', 'cooperation-admin']);
    }

    public function prepareForValidation()
    {
        // get the contents
        $contents = $this->input('content', []);
        $undotedContents = [];

        foreach ($contents as $cid => $data) {
            // undot the array and set it.
            if (array_key_exists('content', $data)) {
                $undotedContents['content'][$cid]['content'] = $this->array_undot($data['content']);
                $undotedContents['content'][$cid]['build_year'] = $data['build_year'];
            }
        }

        // modify the request.
        $this->replace(array_replace($this->all(), $undotedContents));
    }

    public function failedValidation(Validator $validator)
    {
        // use the old helper since we have modified the request.
        Old::put($this->all());

        parent::failedValidation($validator);
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

    protected function array_undot($content)
    {
        $array = [];

        foreach ($content as $key => $values) {
            foreach ($values as $dottedKey => $value) {
                array_set($array, $key.'.'.$dottedKey, $value);
            }
        }

        return $array;
    }
}
