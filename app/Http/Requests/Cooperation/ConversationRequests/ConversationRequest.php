<?php

namespace App\Http\Requests\Cooperation\ConversationRequests;

use App\Helpers\HoomdossierSession;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ConversationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check() && ! HoomdossierSession::isUserObserving();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'action' => 'sometimes|required',
            'message' => 'required',
        ];
    }
}
