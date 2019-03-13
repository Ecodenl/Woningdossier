<?php

namespace App\Http\Requests\Cooperation\Admin;

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
        return \Auth::user()->hasRoleAndIsCurrentRole(['coach', 'coordinator', 'cooperation-admin']);
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
            'status' => [Rule::in([
                BuildingCoachStatus::STATUS_NO_EXECUTION, BuildingCoachStatus::STATUS_EXECUTED, BuildingCoachStatus::STATUS_IN_PROGRESS,
                BuildingCoachStatus::STATUS_PENDING, BuildingCoachStatus::STATUS_REMOVED,
            ])]
        ];
    }
}
