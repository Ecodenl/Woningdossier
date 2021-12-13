<?php

namespace App\Http\Requests\Cooperation\Tool;

use App\Helpers\ConsiderableHelper;
use App\Http\Requests\DecimalReplacementTrait;
use App\Models\Interest;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class InsulatedGlazingFormRequest extends FormRequest
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

    public function prepareForValidation()
    {
        $this->decimals([
            'building_insulated_glazings' => 'm2',
            'window_surface',
        ]);
    }

    public function rules()
    {
        $max = Carbon::now()->year;
        /** @var Collection $noInterests */

        // m2 and window rules in the withValidator
        $rules = [
            'considerables.*.is_considering' => ['required', Rule::in(array_keys(ConsiderableHelper::getConsiderableValues()))],
            'building_elements.*' => 'required|exists:element_values,id',
            'building_elements.*.*' => 'exists:element_values,id',
            'building_features.window_surface' => 'nullable|numeric|min:1',
            'building_paintwork_statuses.wood_rot_status_id' => 'required|exists:wood_rot_statuses,id',
            'building_paintwork_statuses.paintwork_status_id' => 'required|exists:paintwork_statuses,id',
            'building_paintwork_statuses.last_painted_year' => 'nullable|numeric|between:1990,'.$max,
        ];

        return $rules;
    }

    public function withValidator(Validator $validator)
    {
        $big = 'building_insulated_glazings.';

        foreach ($this->get('considerables') as $considerableId => $considerData) {

            // if the user considers the measure we will add the rules, else we wont.
            if ($considerData['is_considering']) {
                $validator->addRules([
                    $big.$considerableId.'.m2' => 'required|numeric|min:1',
                    $big.$considerableId.'.windows' => 'required|numeric|min:1',
                ]);
            } else {
                $validator->addRules([
                    $big.$considerableId.'.m2' => 'nullable|numeric|min:1',
                    $big.$considerableId.'.windows' => 'nullable|numeric|min:1',
                ]);
            }
        }
    }
}
