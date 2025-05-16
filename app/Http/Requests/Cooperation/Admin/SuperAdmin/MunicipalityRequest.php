<?php

namespace App\Http\Requests\Cooperation\Admin\SuperAdmin;

use App\Models\Municipality;
use App\Rules\UniqueSlug;
use Illuminate\Foundation\Http\FormRequest;

class MunicipalityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return \App\Helpers\Hoomdossier::user()->hasRoleAndIsCurrentRole('super-admin');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'municipalities.name' => ['required', new UniqueSlug(Municipality::class, 'short', $this->route('municipality'))],
        ];
    }
}
