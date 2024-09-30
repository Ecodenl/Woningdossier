<?php

namespace App\Http\Requests\Cooperation\Admin\SuperAdmin;

use App\Helpers\Arr;
use App\Helpers\Wrapper;
use App\Models\MeasureCategory;
use App\Rules\LanguageRequired;
use App\Rules\UniqueSlug;
use App\Services\Verbeterjehuis\RegulationService;
use Illuminate\Foundation\Http\FormRequest;

class MeasureCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return \App\Helpers\Hoomdossier::user()->hasRoleAndIsCurrentRole('super-admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'measure_categories.name' => ['required', new LanguageRequired()],
            'measure_categories.name.*' => ['nullable', new UniqueSlug(MeasureCategory::class, 'short', $this->route('measureCategory'))],
            'vbjehuis_measure' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    // So the value is not null, but is it valid?
                    $measures = Wrapper::wrapCall(fn () => RegulationService::init()->getFilters()['Measures']) ?? [];
                    $targetData = Arr::first(Arr::where($measures, fn ($a) => $a['Value'] == $value));

                    // Incorrect value passed
                    if (empty($targetData)) {
                        $fail(__('validation.custom-rules.api.incorrect-vbjehuis-value'));
                    }
                },
            ],
        ];
    }
}
