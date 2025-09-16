<?php

namespace App\Http\Requests\Cooperation\MyAccount;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NotificationSettingsFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return \Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'notification_setting.*.interval_id' => ['int', 'required', Rule::exists('notification_intervals', 'id')],
        ];
    }
}
