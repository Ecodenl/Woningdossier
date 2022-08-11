<?php

namespace App\Http\Requests\Api\V1\Cooperation;

use App\Helpers\RoleHelper;
use App\Http\Requests\Api\ApiRequest;
use App\Models\User;

class BuildingCoachStatusFormRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'building_coach_statuses.coach_contact_id' => ['required', 'numeric', 'integer', 'gt:0'],
            'building_coach_statuses.resident_contact_id' => ['required', 'numeric', 'integer', 'gt:0'],
        ];
    }

    public function messages()
    {
        return [
            'allow_access.required' => __('auth.register.validation.allow_access'),
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $coachContactId = $this->input('building_coach_statuses.coach_contact_id');
            $residentContactId = $this->input('building_coach_statuses.resident_contact_id');

            // Due to above validation we can assume these values are now correct
            if (! empty($coachContactId) && ! empty($residentContactId)) {
                $coach = User::byContact($coachContactId)->first();
                if (! $coach instanceof User) {
                    $validator->errors()->add('building_coach_statuses.coach_contact_id', __('validation.custom.contact-id.not-found'));
                } elseif($coach->hasNotRole(RoleHelper::ROLE_COACH)) {
                    $validator->errors()->add('building_coach_statuses.coach_contact_id', __('validation.custom.users.incorrect-role', ['role' => RoleHelper::ROLE_COACH]));
                }

                $resident = User::byContact($residentContactId)->first();
                if (! $resident instanceof User) {
                    $validator->errors()->add('building_coach_statuses.resident_contact_id', __('validation.custom.contact-id.not-found'));
                }
            }
        });
    }
}
