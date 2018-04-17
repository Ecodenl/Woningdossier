<?php

namespace App\Http\Requests;

use App\Models\Interest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InsulatedGlazingFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    public function rules()
    {

        $rules = [
            'user_interest.*' => 'required|exists:interests,id',
            'building_elements.*' => 'required|exists:element_values,id',
            'building_elements.*.*' => 'exists:element_values,id',
            'building_insulated_glazings.*.m2' => 'nullable|numeric',
            'building_insulated_glazings.*.windows' => 'nullable|numeric',

            'building_paintwork_statuses.wood_rot_status_id' => 'required|exists:wood_rot_statuses,id',
            'building_paintwork_statuses.paintwork_status_id' => 'required|exists:paintwork_statuses,id',
            'building_paintwork_statuses.last_painted_year' => 'nullable|numeric|digits_between:4,4',

        ];

        return $rules;

    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            foreach ($this->request->get('user_interests') as $userInterestId => $userInterest) {

                // Get the search field
                $interest = Interest::find($userInterest);

                // Get the field values
                $m2 = Request::input('building_insulated_glazings.' . $userInterestId . '.m2', '');
                $totalWindows = Request::input('building_insulated_glazings.' . $userInterestId . '.windows', '');

                // Check if the interest fields are filled
                if ($m2 == "" && ($interest->calculate_value == "1" || $interest->calculate_value == "2" || $interest->calculate_value == "3")) {
                    $validator->errors()->add('building_insulated_glazings.' . $userInterestId . '.m2', __('validation.custom.needs-to-be-filled'));
                }
                if ($totalWindows == "" && ($interest->calculate_value == "1" || $interest->calculate_value == "2" || $interest->calculate_value == "3")) {
                    $validator->errors()->add('building_insulated_glazings.' . $userInterestId . '.windows', __('validation.custom.needs-to-be-filled'));
                }
            }
        });
    }
}
