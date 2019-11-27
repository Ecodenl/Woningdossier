<?php

namespace App\Http\Requests\Cooperation\Tool\GeneralData;

use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Fluent;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class CurrentStateFormRequest extends FormRequest
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
            'elements.*.element_id' => ['required', Rule::exists('elements', 'id')],
            'elements.*.element_value_id' => ['required', Rule::exists('element_values', 'id')],

            'services.*.service_id' => ['required', Rule::exists('services', 'id')],
            'services.*.service_value_id' => ['required', Rule::exists('service_values', 'id')],
            // its not possible to have a service_value_id for this service value. So we add the sometimes rule to bypass the services.*.service_value_id required rule.
            'services.total-sun-panels.service_value_id' => 'sometimes',
            'services.total-sun-panels.extra.value' => 'nullable|numeric|min:0|max:50',
            'services.total-sun-panels.extra.year' => 'nullable|numeric|between:1980,' . $max,
            'services.house-ventilation.extra.demand_driven' => 'sometimes|accepted',
            'services.house-ventilation.extra.heat_recovery' => 'sometimes|accepted',
            'building_features.building_heating_application_id' => ['required', Rule::exists('building_heating_applications', 'id')],
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->sometimes('building_pv_panels.total_installed_power', 'nullable|numeric|max:18000|min:0', function (Fluent $input) {
            $input = Arr::dot($input->getAttributes());
            return $input['services.total-sun-panels.extra.value'] > 0;
        });
    }
}
