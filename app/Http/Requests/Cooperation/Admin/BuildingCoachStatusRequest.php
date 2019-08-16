<?php

namespace App\Http\Requests\Cooperation\Admin;

use App\Models\Building;
use App\Models\BuildingCoachStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BuildingCoachStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \App\Helpers\Hoomdossier::user()->hasRoleAndIsCurrentRole(['coach', 'coordinator', 'cooperation-admin']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'building_id' => [
                Rule::exists('buildings', 'id')
            ],
            'status_id' => [
                Rule::exists('statuses', 'id')
            ]
        ];
    }
}
