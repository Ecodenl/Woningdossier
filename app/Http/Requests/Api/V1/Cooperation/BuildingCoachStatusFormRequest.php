<?php

namespace App\Http\Requests\Api\V1\Cooperation;

use App\Helpers\RoleHelper;
use App\Http\Requests\Api\ApiRequest;
use App\Models\User;
use App\Services\BuildingCoachStatusService;

class BuildingCoachStatusFormRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'building_coach_statuses.coach_contact_id' => ['required', 'numeric', 'integer', 'gt:0'],
            'building_coach_statuses.resident_contact_id' => ['required', 'numeric', 'integer', 'gt:0'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $coachContactId = $this->input('building_coach_statuses.coach_contact_id');
            $residentContactId = $this->input('building_coach_statuses.resident_contact_id');

            // Because for some reason Laravel won't do this for us???
            $coachAttr = __('validation.attributes')['building_coach_statuses.coach_contact_id'];
            $residentAttr = __('validation.attributes')['building_coach_statuses.resident_contact_id'];

            // Due to above validation we can assume these values are now correct
            if (! empty($coachContactId) && ! empty($residentContactId)) {
                // By querying on the User model, it is automatically scoped to the cooperation
                $coach = User::byContact($coachContactId)->first();
                $resident = User::byContact($residentContactId)->first();

                if (! $coach instanceof User) {
                    $validator->errors()
                        ->add(
                            'building_coach_statuses.coach_contact_id',
                            __('validation.custom.contact-id.not-found', ['attribute' => $coachAttr])
                        );
                } elseif ($coach->hasNotRole(RoleHelper::ROLE_COACH)) {
                    $validator->errors()
                        ->add(
                            'building_coach_statuses.coach_contact_id',
                            __('validation.custom.users.incorrect-role', [
                                'attribute' => $coachAttr, 'role' => RoleHelper::ROLE_COACH
                            ])
                        );
                } elseif ($resident instanceof User) {
                    $connectedCoaches = BuildingCoachStatusService::getConnectedCoachesByBuilding($resident->building);
                    $foundCoach = $connectedCoaches->first(function ($connectedCoach) use ($coach) {
                        return $connectedCoach->coach_id === $coach->id;
                    });

                    if (! is_null($foundCoach)) {
                        $validator->errors()
                            ->add(
                                'building_coach_statuses.coach_contact_id',
                                __('validation.custom.building-coach-statuses.already-linked')
                            );
                    }
                }

                if (! $resident instanceof User) {
                    $validator->errors()
                        ->add(
                            'building_coach_statuses.resident_contact_id',
                            __('validation.custom.contact-id.not-found', ['attribute' => $residentAttr])
                        );
                } elseif (! $resident->allowedAccess()) {
                    $validator->errors()
                        ->add(
                            'building_coach_statuses.resident_contact_id',
                            __('validation.custom.building-coach-statuses.no-access')
                        );
                }
            }
        });
    }
}
