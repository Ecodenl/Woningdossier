<?php

namespace App\Http\Requests\Cooperation\Admin\SuperAdmin;

use App\Helpers\Arr;
use App\Models\Mapping;
use App\Models\Municipality;
use App\Services\Verbeterjehuis\RegulationService;
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
                'bail',
                'exists:mappings,id',
                function ($attribute, $value, $fail) {
                    $mapping = Mapping::find($value);

                    if (! is_null($mapping->target_model_id) && $mapping->target_model_id !== $this->municipality->id) {
                        $fail(__('validation.custom-rules.municipalities.already-coupled'));
                    }
                },
            ],
            'vbjehuis_municipality' => [
                'bail',
                'nullable',
                function ($attribute, $value, $fail) {
                    $parts = explode('-', $value, 2);
                    $id = $parts[0] ?? '';
                    $name = $parts[1] ?? '';

                    // So the value is not null, but is it valid?
                    $municipalities = RegulationService::init()->getFilters()['Cities'] ?? [];
                    $targetData = Arr::first(Arr::where($municipalities, fn ($a) => $a['Id'] == $id && $a['Name'] == $name));

                    // Incorrect value passed
                    if (empty($targetData)) {
                        $fail(__('validation.custom-rules.api.incorrect-vbjehuis-value'));
                    } else {
                        $mapping = Mapping::where('target_data->Id', $value)->first();

                        // If mapping not yet existent, we're good
                        if ($mapping instanceof Mapping) {
                            if (! is_null($mapping->from_model_id) && $mapping->from_model_id !== $this->municipality->id) {
                                $fail(__('validation.custom-rules.municipalities.already-coupled'));
                            }
                        }
                    }
                },
            ],
        ];
    }
}
