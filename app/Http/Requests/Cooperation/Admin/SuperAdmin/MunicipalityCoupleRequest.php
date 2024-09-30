<?php

namespace App\Http\Requests\Cooperation\Admin\SuperAdmin;

use App\Helpers\Arr;
use App\Helpers\Wrapper;
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
     */
    public function authorize(): bool
    {
        return \App\Helpers\Hoomdossier::user()->hasRoleAndIsCurrentRole('super-admin');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
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
                'nullable',
                function ($attribute, $value, $fail) {
                    $parts = explode('-', $value, 2);
                    $id = $parts[0] ?? '';
                    $name = $parts[1] ?? '';

                    // So the value is not null, but is it valid?
                    $municipalities = Wrapper::wrapCall(fn () => RegulationService::init()->getFilters()['Cities']) ?? [];
                    $targetData = Arr::first(Arr::where($municipalities, fn ($a) => $a['Id'] == $id && $a['Name'] == $name));

                    // Incorrect value passed
                    if (empty($targetData)) {
                        $fail(__('validation.custom-rules.api.incorrect-vbjehuis-value'));
                    }
                },
            ],
        ];
    }
}
