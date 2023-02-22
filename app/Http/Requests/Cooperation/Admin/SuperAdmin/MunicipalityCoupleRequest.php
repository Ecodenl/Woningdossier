<?php

namespace App\Http\Requests\Cooperation\Admin\SuperAdmin;

use App\Models\Mapping;
use App\Models\Municipality;
use Illuminate\Foundation\Http\FormRequest;

class MunicipalityCoupleRequest extends FormRequest
{
    protected Municipality $municipality;

    public function prepareForValidation()
    {
        $this->municipality = $this->route('municipality');
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \App\Helpers\Hoomdossier::user()->hasRoleAndIsCurrentRole('super-admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'bag_municipalities' => [
                'required',
                'min:1',
            ],
            'bag_municipalities.*' => [
                // User input is always allowed. If existing mapping, it cannot be coupled already.
                function ($attribute, $value, $fail) {
                    $mapping = Mapping::find($value);

                    if ($mapping instanceof Mapping) {
                        if (! is_null($mapping->target_model_id) && $mapping->target_model_id !== $this->municipality->id) {
                            $fail(__('validation.custom-rules.municipalities.already-coupled'));
                        }
                    }
                },
            ],
            'vbjehuis_municipality' => [
                'bail',
                'nullable',
                function ($attribute, $value, $fail) {
                    $mapping = Mapping::where('target_data->Id', $value)->first();

                    // If mapping not yet existent, we're good
                    if ($mapping instanceof Mapping) {
                        if (! is_null($mapping->from_model_id) && $mapping->from_model_id !== $this->municipality->id) {
                            $fail(__('validation.custom-rules.municipalities.already-coupled'));
                        }
                    }
                },
            ],
        ];
    }
}
