<?php

namespace App\Http\Requests\Cooperation\Admin\Cooperation\CooperationAdmin;

use App\Helpers\Hoomdossier;
use App\Helpers\Models\CooperationMeasureApplicationHelper;
use App\Models\CooperationMeasureApplication;
use App\Rules\LanguageRequired;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CooperationMeasureApplicationFormRequest extends FormRequest
{
    protected array $measures = [];
    protected bool $isExtensive;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Auth::check() && Hoomdossier::user()->hasRoleAndIsCurrentRole('cooperation-admin');
    }

    public function prepareForValidation()
    {
        // On create, we have a type. On update we have a model.
        $this->isExtensive = ($measure = $this->route('cooperationMeasureApplication')) instanceof CooperationMeasureApplication
            ? $measure->is_extensive_measure
            : $this->route('type') === CooperationMeasureApplicationHelper::EXTENSIVE_MEASURE;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $evaluateGt = ! is_null($this->input('cooperation_measure_applications.costs.from'));

        return [
            'cooperation_measure_applications.name' => [
                'required', new LanguageRequired('nl'),
            ],
            'cooperation_measure_applications.info' => [
                'required', new LanguageRequired('nl'),
            ],
            'cooperation_measure_applications.measure_category' => [
                'nullable',
                'exists:measure_categories,id',
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
