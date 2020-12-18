<?php

namespace App\Http\Requests\Cooperation\Admin;

use App\Helpers\Hoomdossier;
use App\Helpers\ToolHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Helpers\ExampleBuildingHelper;

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
            // we can use this for the translations of the errors.
            $contentStructure = Arr::dot(ToolHelper::getContentStructure());

            $options = $this->input('content');
            $values = Arr::dot($options, 'content.');

            // Validate numeric fields of the content
            foreach ($values as $name => $value){
                if (!is_null($value) && ExampleBuildingHelper::isNumeric($name)) {
                    $value = str_replace(',', '.', $value);

                    // If surface is not null and surface is not numeric
                    if (!is_null($value) && !is_numeric($value)) {

                        $keys = explode('content.', $name);

                        // remove the . from the cid.
                        $cid = substr($keys[1], 0 ,-1);
                        $contentStructureKey = last($keys);

                        $buildYear = $values["content.{$cid}.build_year"];
                        $label = $contentStructure[$contentStructureKey.".label"];

                        $validator->errors()->add($name, "{$label} ({$buildYear}) Moet een nummer zijn");
                    }
                }
            }

            // Get all build years
            $buildYears = Arr::where($values, function ($value, $key) {
                return Str::endsWith($key,'build_year');
            });

            // Check each
            foreach ($buildYears as $name => $value) {
                // Get cid
                $cid = explode('.', $name)[1];
                // If it's new, it requires different rules
                if ($cid == 'new') {
                    // Check if there's new values
                    $newValues = Arr::where($values, function ($value, $key) {
                        $internalCid = explode('.', $key)[1];

                        if ($internalCid == 'new' && !is_null($value)) {
                            return $this;
                        }
                    });

                    // There is 9 values with predefined functions that cannot be set to null
                    // This means, if there's more than 9, something has been filled.
                    // This is when the build year will get an error.
                    if (count($newValues) > 9 && empty($value)) {
                        $validator->errors()->add($name, 'Bouwjaar voor nieuwe content kan niet leeg zijn');
                    }
                }
                else if (empty($value)) {
                    $validator->errors()->add($name, 'Bouwjaar kan niet leeg zijn');
                }
            }
        });
    }
}

//'content.*.build_year' => 'required',
