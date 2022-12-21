<?php

namespace App\Http\Requests\Cooperation\Admin\SuperAdmin;

use App\Helpers\Hoomdossier;
use App\Models\MeasureApplication;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\LanguageRequired;
use Illuminate\Support\Facades\Auth;

class MeasureApplicationFormRequest extends FormRequest
{
    protected MeasureApplication $measureApplication;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check() && Hoomdossier::user()->hasRoleAndIsCurrentRole('super-admin');
    }

    public function prepareForValidation()
    {
        $this->measureApplication = $this->route('measureApplication');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $evaluateGt = ! is_null($this->input('measure_applications.cost_range.from'));

        $nonCalcRules = [
            'measure_applications.cost_range.from' => [
                'nullable', 'numeric', 'min:0',
            ],
            'measure_applications.cost_range.to' => [
                'required', 'numeric', 'min:0', $evaluateGt ? 'gt:measure_applications.cost_range.from' : '',
            ],
            'measure_applications.savings_money' => [
                'required', 'numeric', 'min:0',
            ],
        ];

        $rules = [
            'measure_applications.measure_name' => [
                new LanguageRequired('nl'),
            ],
            'measure_applications.measure_info' => [
                new LanguageRequired('nl'),
            ],
            'measure_applications.configurations.icon' => [
                'required',
            ],
        ];

        if (! $this->measureApplication->has_calculations) {
            $rules = array_merge($rules, $nonCalcRules);
        }

        return $rules;
    }
}
