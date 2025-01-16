<?php

namespace App\Http\Requests\Api\V1\Cooperation;

use App\Helpers\RoleHelper;
use App\Helpers\ToolQuestionHelper;
use App\Http\Requests\Api\ApiRequest;
use App\Models\Account;
use App\Models\Cooperation;
use App\Models\ToolQuestion;
use App\Rules\HouseNumber;
use App\Rules\HouseNumberExtension;
use App\Rules\PhoneNumber;
use App\Rules\PostalCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class RegisterFormRequest extends ApiRequest
{
    private ?Cooperation $cooperation = null;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * so fields can be modified or added before validation.
     */
    public function prepareForValidation(): void
    {
        $this->cooperation = $this->route('cooperation');

        // Add new data field before it gets sent to the validator
        $this->merge([
            'house_number_extension' => strtolower(preg_replace("/[\s-]+/", '', $this->get('house_number_extension', ''))),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'email' => 'required|string|email|max:255|unique:accounts',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'postal_code' => ['required', new PostalCode('nl')],
            'number' => ['required', 'integer', new HouseNumber('nl')],
            'house_number_extension' => [new HouseNumberExtension('nl')],
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'phone_number' => ['nullable', new PhoneNumber('nl')],
            'extra.contact_id' => ['nullable', 'numeric', 'integer', 'gt:0'],

            'roles' => ['nullable', 'array'],
            'roles.*' => ['string', Rule::in([RoleHelper::ROLE_RESIDENT, RoleHelper::ROLE_COACH])],
        ];

        // try to get the account
        $account = Account::where('email', $this->get('email'))->first();
        // if the account exists but the user is not associated with the current cooperation
        // then we unset the email and password rule because we dont need to validate them, we handle them in the controller
        if ($account instanceof Account && ! $account->isAssociatedWith($this->cooperation)) {
            unset($rules['email']);
        }

        $toolQuestionAnswers = $this->input('tool_questions', []);
        foreach ($toolQuestionAnswers as $toolQuestionShort => $toolQuestionAnswer) {
            if (in_array($toolQuestionShort, ToolQuestionHelper::SUPPORTED_API_SHORTS)) {
                $toolQuestion = ToolQuestion::findByShort($toolQuestionShort);
                if ($toolQuestion instanceof ToolQuestion) {
                    $rules["tool_questions.{$toolQuestionShort}"] = $toolQuestion->validation;
                }
            }
        }

        return $rules;
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function (Validator $validator) {
            $contactId = $this->input('extra.contact_id');
            // If contact_id is set we want to ensure it's unique PER cooperation
            if (! empty($contactId)) {
                $query = DB::table('users')->where('extra->contact_id', $contactId)
                    ->where('cooperation_id', $this->cooperation->id);

                // We found a user, so one exists with this contact ID
                if ($query->first() instanceof \stdClass) {
                    $validator->errors()->add('extra.contact_id', __('validation.unique', [
                        'attribute' => __('validation.attributes')['users.extra.contact_id'],
                    ]));
                }
            }
        });
    }
}
