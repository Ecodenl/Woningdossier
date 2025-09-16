<?php

namespace App\Http\Requests;

use App\Rules\PhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class MyAccountSettingsFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        /** @var \App\Models\Cooperation $cooperation */
        $cooperation = $this->route('cooperation');

        return array_merge([
            'user.first_name' => 'required|string|max:255',
            'user.last_name' => 'required|string|max:255',
            'user.phone_number' => ['nullable', new PhoneNumber()],
        ], (new AddressFormRequest())->setCountry($cooperation->country)->rules());
    }
}
