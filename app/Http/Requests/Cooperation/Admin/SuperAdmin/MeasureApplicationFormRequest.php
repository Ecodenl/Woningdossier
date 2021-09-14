<?php

namespace App\Http\Requests\Cooperation\Admin\SuperAdmin;

use App\Helpers\Hoomdossier;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\LanguageRequired;
use Illuminate\Support\Facades\Auth;

class MeasureApplicationFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check() && Hoomdossier::user()->hasRoleAndIsCurrentRole('super-admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'measure_applications.measure_name' => [
                new LanguageRequired('nl'),
            ],
            'measure_applications.measure_info' => [
                new LanguageRequired('nl'),
            ],
            'measure_applications.configurations.icon' => [
                'required',
            ],
        ];
    }
}
