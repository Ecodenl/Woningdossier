<?php

namespace App\Http\Requests\Cooperation\Admin;

use App\Helpers\Hoomdossier;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use App\Helpers\ContentHelper;

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
                if (!is_null($value) && ContentHelper::isNumeric($name)) {
                    $value = str_replace(',', '.', $value);

                    // If surface is not null and surface is not numeric
                    if (!is_null($value) && !is_numeric($value)) {
                        $validator->errors()->add($name, 'Item moet een nummer zijn');
                    }
                }
            }
        });
    }
}
