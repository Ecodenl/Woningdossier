<?php

namespace App\Http\Requests\Cooperation\Tool\GeneralData;

use App\Models\Step;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InterestFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_interests.*.interested_in_type' => [Rule::in([Step::class])],
            'user_interests.*.interest_id' => [Rule::exists('interests', 'id')],
            'user_interests.*.interest_in_id' => [Rule::exists('steps', 'id')],
            'user_energy_habits.renovation_plans' => ['required', Rule::in([1, 2, 0])],
            'user_energy_habits.building_complaints' => 'max:100000',
            'user_motivations.id' => ['required', Rule::exists('motivations', 'id')],
        ];
    }
}
