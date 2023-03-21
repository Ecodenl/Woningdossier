<?php

namespace App\Http\Requests\Cooperation\Tool;

use App\Helpers\ConsiderableHelper;
use App\Http\Requests\DecimalReplacementTrait;
use App\Models\Step;
use App\Models\ToolQuestion;
use App\Rules\ValidateElementKey;
use App\Services\LegacyService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class FloorInsulationFormRequest extends FormRequest
{
    use DecimalReplacementTrait;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    public function getValidatorInstance()
    {
        $this->decimals(['building_features.floor_surface', 'building_features.insulation_surface']);

        return parent::getValidatorInstance();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $noDatabaseSelectOptions = ['yes', 'no', 'unknown'];

        $measureRelatedShorts = LegacyService::init()->getToolQuestionShorts(Step::findByShort('floor-insulation'));

        $rules = [
            'considerables.*.is_considering' => ['required', Rule::in(array_keys(ConsiderableHelper::getConsiderableValues()))],
            'element' => ['exists:element_values,id', new ValidateElementKey('floor-insulation')],
            'building_elements.extra.access' => ['nullable', 'alpha', Rule::in($noDatabaseSelectOptions)],
            'building_elements.extra.has_crawlspace' => ['nullable', 'alpha', Rule::in($noDatabaseSelectOptions)],
            'building_elements.element_value_id' => 'exists:element_values,id',
            'building_features.floor_surface' => 'required|numeric|min:1|max:100000',
            'building_features.insulation_surface' => 'required|numeric|min:0|needs_to_be_lower_or_same_as:building_features.floor_surface',
        ];

        foreach ($measureRelatedShorts as $tqShorts) {
            $toolQuestions = ToolQuestion::findByShorts($tqShorts);
            foreach ($toolQuestions as $toolQuestion) {
                $rules[$toolQuestion->short] = $toolQuestion->validation;
            }
        }

        return $rules;
    }
}
