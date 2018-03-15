<?php

namespace App\Http\Requests;

use App\Rules\PhoneNumber;
use Illuminate\Foundation\Http\FormRequest;

class MyAccountSettingsFormRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return \Auth::check();
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'password' => 'nullable|string|confirmed|min:6',
			'first_name' => 'required|string|max:255',
			'last_name' => 'required|string|max:255',
			'phone_number' => [ 'nullable', new PhoneNumber() ],
		];
	}
}
