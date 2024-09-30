<?php

namespace App\Http\Requests\Cooperation\Admin\Coach;

use App\Rules\isUserMemberOfCooperation;
use Illuminate\Foundation\Http\FormRequest;

class ConnectToResidentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return \App\Helpers\Hoomdossier::user();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => 'required',
            'message' => 'required',
            'receiver_id' => ['required', 'exists:users,id', new isUserMemberOfCooperation()],
        ];
    }
}
