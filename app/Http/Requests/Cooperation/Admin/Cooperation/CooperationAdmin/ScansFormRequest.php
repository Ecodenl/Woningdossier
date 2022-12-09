<?php

namespace App\Http\Requests\Cooperation\Admin\Cooperation\CooperationAdmin;

use App\Helpers\Models\CooperationSettingHelper;
use App\Services\CooperationScanService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ScansFormRequest extends FormRequest
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
            'scans.type' => Rule::in(
                array_keys(CooperationScanService::translationMap())
            )
        ];
    }
}
