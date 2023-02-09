<?php

namespace App\Http\Requests\Cooperation\Admin\Cooperation\CooperationAdmin;

use App\Helpers\Hoomdossier;
use App\Helpers\Models\CooperationMeasureApplicationHelper;
use App\Models\CooperationMeasureApplication;
use App\Rules\LanguageRequired;
use App\Services\Verbeterjehuis\RegulationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

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

        // On create, we have a type. On update we have a model.
        $isExtensive = ($measure = $this->route('cooperationMeasureApplication')) instanceof CooperationMeasureApplication
            ? $measure->is_extensive_measure
            : $this->route('type') === CooperationMeasureApplicationHelper::EXTENSIVE_MEASURE;

        return [
            'cooperation_measure_applications.name' => [
                new LanguageRequired('nl'),
            ],
            'cooperation_measure_applications.info' => [
                new LanguageRequired('nl'),
            ],
            'cooperation_measure_applications.measure_category' => [
                Rule::requiredIf(! $isExtensive),
                Rule::in(Arr::pluck(RegulationService::init()->getFilters()['Measures'], 'Value')),
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
