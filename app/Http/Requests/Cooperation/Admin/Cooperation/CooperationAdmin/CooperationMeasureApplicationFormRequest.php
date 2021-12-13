<?php

namespace App\Http\Requests\Cooperation\Admin\Cooperation\CooperationAdmin;

use App\Helpers\Hoomdossier;
use App\Rules\LanguageRequired;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CooperationMeasureApplicationFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check() && Hoomdossier::user()->hasRoleAndIsCurrentRole('cooperation-admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $evaluateGt = ! is_null($this->input('cooperation_measure_applications.costs.from'));

        return [
            'cooperation_measure_applications.name' => [
                new LanguageRequired('nl'),
            ],
            'cooperation_measure_applications.info' => [
                new LanguageRequired('nl'),
            ],
            'cooperation_measure_applications.costs.from' => [
                'nullable', 'numeric', 'min:0',
            ],
            'cooperation_measure_applications.costs.to' => [
                'required', 'numeric', 'min:0', $evaluateGt ? 'gt:cooperation_measure_applications.costs.from' : '',
            ],
            'cooperation_measure_applications.savings_money' => [
                'required', 'numeric', 'min:0',
            ],
            'cooperation_measure_applications.extra.icon' => [
                'required',
            ],
        ];
    }
}
