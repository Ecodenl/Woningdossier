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
            'scans.type' => Rule::in(
                array_keys(CooperationScanService::translationMap())
            )
        ];
    }
}
