<?php

namespace App\Http\Requests\Cooperation;

use App\Models\Questionnaire;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class FileStorageFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
        ];
    }

    public function withValidator(Validator $validator)
    {
        // check whether there is a questionnaire and when given check if it belongs to the current cooperation.
        if ($this->has('file_storages.questionnaire_id')) {
            $validator->after(function ($v) {
                $questionnaire = Questionnaire::find(
                    $this->input('file_storages.questionnaire_id')
                );
                if (! $questionnaire instanceof Questionnaire) {
                    $v->errors()->add('file_storages.questionnaire_id', 'Invalid.');
                }
            });
        }
    }
}
