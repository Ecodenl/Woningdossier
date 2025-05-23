<?php

namespace App\Http\Requests\Cooperation\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BuildingNoteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return \App\Helpers\Hoomdossier::user()->hasRoleAndIsCurrentRole(['coach', 'coordinator', 'cooperation-admin']);
    }

    public function prepareForValidation(): void
    {
        $this->redirect = url()->previous() . '#building-notes';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'building.note' => 'required',
            'building.id' => ['required', Rule::exists('buildings', 'id')],
        ];
    }
}
