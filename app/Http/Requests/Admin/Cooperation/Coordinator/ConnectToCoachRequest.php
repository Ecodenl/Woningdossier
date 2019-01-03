<?php

namespace App\Http\Requests\Admin\Cooperation\Coordinator;

use App\Rules\isUserMemberOfCooperation;
use Illuminate\Foundation\Http\FormRequest;

class ConnectToCoachRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \Auth::user()->hasRole('coordinator');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
//            'title' => 'required|max:125',
//            'message' => 'required',
            'coach' => ['required', 'exists:users,id', new isUserMemberOfCooperation],
        ];
    }
}
