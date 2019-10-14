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
        $this->decimals([
            'building_insulated_glazings' => 'm2',
            'window_surface'
        ]);
    }


    public function rules()
    {
        $max = Carbon::now()->year;
        /** @var Collection $noInterests */

        // m2 and window rules in the withValidator
        $rules = [
            'building_elements.*' => 'required|exists:element_values,id',
            'building_elements.*.*' => 'exists:element_values,id',
            'window_surface' => 'numeric|min:1',
            'building_paintwork_statuses.wood_rot_status_id' => 'required|exists:wood_rot_statuses,id',
            'building_paintwork_statuses.paintwork_status_id' => 'required|exists:paintwork_statuses,id',
            'building_paintwork_statuses.last_painted_year' => 'nullable|numeric|between:1990,' . $max,
        ];

        return $rules;
    }

    public function withValidator(Validator $validator)
    {
        $big = 'building_insulated_glazings.';

        foreach ($this->get('user_interests') as $measureApplicationId => $userInterest) {
            // when the user his interest level is high enough, we should add rules. Otherwise there is no need to validate the fields.
            if ($this->isUserInterested($userInterest)) {
                $validator->addRules([
                    $big . $measureApplicationId . '.m2' => 'numeric|min:1',
                    $big . $measureApplicationId . '.windows' => 'numeric|min:1'
                ]);
            }
        }
    }

    /**
     * Check whether the user has interest in the particular measure.
     *
     * @param $userInterest
     * @return bool
     */
    public function isUserInterested($userInterest)
    {
        $noInterestIds = Interest::where('calculate_value', 4)->orWhere('calculate_value', 5)->select('id')->get()->pluck('id')->toArray();

        return !in_array($userInterest, $noInterestIds);
    }
}
