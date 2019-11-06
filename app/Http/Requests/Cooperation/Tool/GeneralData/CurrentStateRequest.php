<?php

namespace App\Http\Requests\Cooperation\Tool\GeneralData;

use App\Models\Service;
use Carbon\Carbon;
use function Couchbase\defaultDecoder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class CurrentStateRequest extends FormRequest
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
        return [
            'element.*' => 'required|exists:element_values,id',
            'building_features.building_heating_application_id' => ['required', Rule::exists('building_heating_applications', 'id')],
            'building_pv_panels.total_installed_power' => [Rule::requiredIf($this->input('service.7.extra.value') > 0)]
        ];

    }

    public function withValidator(Validator $validator)
    {
        $serviceRules = [];
        $max = Carbon::now()->year;

        foreach ($this->get('service') as $serviceId => $serviceValueId) {
            $inputName = 'service.'.$serviceId;
            $service = Service::find($serviceId)->load('values');
            if ($service instanceof Service) {

                // when the service has values, they should exist. When a service has values its most likely to be a dropdown, otherwise its just an input.
                if ($service->values->isNotEmpty()) {
                    $serviceRules[$inputName] = 'required|exists:service_values,id';
                }

                switch ($service->short) {
                    case 'house-ventilation':
                        $serviceRules[$inputName.'.extra.year'] = 'nullable|numeric|between:1960,' . $max;
                        break;
                    case 'total-sun-panels':
                        // the total sun panel input
                        $serviceRules[$inputName.'extra.value'] = 'nullable|numeric|min:0|max:50';
                        // the year for the sun panels
                        $serviceRules[$inputName.'.extra.year'] = 'nullable|numeric|between:1980,' . $max;
                        break;
                }
            }
        }

        $validator->addRules($serviceRules);
    }

}
