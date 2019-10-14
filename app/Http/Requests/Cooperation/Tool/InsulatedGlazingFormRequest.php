<?php

namespace App\Http\Requests\Cooperation\Tool;

use App\Http\Requests\DecimalReplacementTrait;
use App\Models\Interest;
use App\Models\MeasureApplication;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Fluent;
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
        $this->decimals(
            [
                'building_insulated_glazings' => 'm2',
                'window_surface'
            ]);
    }

    /**
     * Check whether the user has interest in the particular measure.
     *
     * @param $userInterest
     * @return bool
     */
    public function isUserInterestedInMeasure($userInterest)
    {
        $noInterestIds = Interest::where('calculate_value', 4)->orWhere('calculate_value', 5)->select('id')->get()->pluck('id')->toArray();

        return !in_array($userInterest, $noInterestIds);
    }
    public function rules()
    {
        $max = Carbon::now()->year;
        /** @var Collection $noInterests */


        $rules = [
            'building_elements.*' => 'required|exists:element_values,id',
            'building_elements.*.*' => 'exists:element_values,id',
//            'building_insulated_glazings.*.m2' => ['nullable', 'numeric', function ($attribute, $value, $fail) {
//                $measureApplicationId = explode('.', $attribute)[1];
//                $userInterestIdForMeasure = $this->input('user_interests.'.$measureApplicationId);
//                dd($this->isUserInterestedInMeasure($userInterestIdForMeasure));
//                if ($this->isUserInterestedInMeasure($userInterestIdForMeasure) && $value >= 1) {
//                    $fail($attribute.' is niet goed ingevuld');
//                }
//            }],
//            user_interests[9]
//            'building_insulated_glazings.*.windows' => 'nullable|numeric|min:1',
            'window_surface' => 'numeric|min:1',

            'building_paintwork_statuses.wood_rot_status_id' => 'required|exists:wood_rot_statuses,id',
            'building_paintwork_statuses.paintwork_status_id' => 'required|exists:paintwork_statuses,id',
            'building_paintwork_statuses.last_painted_year' => 'nullable|numeric|between:1990,'.$max,
        ];

        return $rules;
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $big = 'building_insulated_glazings.';
            foreach ($this->request->get('user_interests') as $measureApplicationId => $userInterest) {

                // Get the field values
                $m2ValueForCurrentMeasureApplication = $this->input($big.$measureApplicationId.'.m2');
                $totalWindowsForCurrentMeasureApplication = $this->input($big.$measureApplicationId.'.windows');


                // when the user has interest in the measure, the m2 and windows field should be validated
                // Check if the interest fields are filled
                if (empty($m2ValueForCurrentMeasureApplication) && $this->isUserInterestedInMeasure($measureApplicationId)) {
                    $validator->errors()->add($big.$measureApplicationId.'.m2', __('validation.custom.needs-to-be-filled'));
                }
                if (empty($totalWindowsForCurrentMeasureApplication) && $this->isUserInterestedInMeasure($measureApplicationId)) {
                    $validator->errors()->add($big.$measureApplicationId.'.windows', __('validation.custom.needs-to-be-filled'));
                }
            }
        });
    }
}
