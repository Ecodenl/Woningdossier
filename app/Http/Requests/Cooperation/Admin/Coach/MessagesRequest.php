<?php

namespace App\Http\Requests\Cooperation\Admin\Coach;

use App\Rules\isUserMemberOfCooperation;
use Illuminate\Foundation\Http\FormRequest;

class MessagesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \Auth::user();
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
            'receiver_id' => ['required', new isUserMemberOfCooperation]
        ];
    }
}
