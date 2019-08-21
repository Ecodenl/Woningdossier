<?php

namespace App\Http\Requests\Cooperation\Admin;

use Illuminate\Foundation\Http\FormRequest;

class MessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * This request will handle all the messages that are send through the admin environment
     *
     * @return bool
     */
    public function authorize()
    {
        return \App\Helpers\Hoomdossier::user()->hasRoleAndIsCurrentRole(['cooperation-admin', 'coordinator', 'coach']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'message' => 'required',
        ];
    }
}
