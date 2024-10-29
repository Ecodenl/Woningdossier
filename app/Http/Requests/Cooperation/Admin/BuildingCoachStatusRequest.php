<?php

namespace App\Http\Requests\Cooperation\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BuildingCoachStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return \App\Helpers\Hoomdossier::user()->hasRoleAndIsCurrentRole(['coach', 'coordinator', 'cooperation-admin']);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'building_id' => [
                Rule::exists('buildings', 'id'),
            ],
            'status_id' => [
                Rule::exists('statuses', 'id'),
            ],
        ];
    }
}
