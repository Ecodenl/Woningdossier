<?php

namespace App\Http\Requests\Cooperation\ConversationRequests;

use App\Helpers\HoomdossierSession;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ConversationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && ! HoomdossierSession::isUserObserving();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'action' => 'sometimes|required',
            'message' => 'required',
        ];
    }
}
