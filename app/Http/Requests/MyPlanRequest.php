<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MyPlanRequest extends FormRequest
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
            'comment' => 'sometimes|required',
        ];
    }
}
