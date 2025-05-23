<?php

namespace App\Http\Requests\Cooperation\Admin;

use App\Http\Requests\AddressFormRequest;
use App\Models\Account;
use App\Models\User;
use App\Rules\PhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class BuildingFormRequest extends FormRequest
{
    /**
     * @var Account
     */
    private $account = null;
    private $user = null;
    private $cooperation = null;

    public function prepareForValidation()
    {
        $this->account = $this->route('building')->user->account;
        $this->user = $this->route('building')->user;
        $this->cooperation = $this->route('cooperation');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return array_merge([
            'accounts.email' => ['required', 'email', Rule::unique('accounts', 'email')->ignore($this->account)],
            'users.first_name' => 'required|string|max:255',
            'users.last_name' => 'required|string|max:255',
            'users.phone_number' => ['nullable', new PhoneNumber('nl')],
            'users.extra.contact_id' => ['nullable', 'numeric', 'integer', 'gt:0'],
        ], (new AddressFormRequest())->setCountry($this->cooperation->country)->rules());
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function (Validator $validator) {
            $contactId = $this->input('users.extra.contact_id');
            // If contact_id is set we want to ensure it's unique PER cooperation
            if (! empty($contactId)) {
                $query = DB::table('users')->where('extra->contact_id', $contactId)
                    ->where('cooperation_id', $this->cooperation->id);

                if ($this->user instanceof User) {
                    $query->where('id', '!=', $this->user->id);
                }

                // We found a user, so one exists with this contact ID
                if ($query->first() instanceof \stdClass) {
                    $validator->errors()->add('users.extra.contact_id', __('validation.unique', [
                        'attribute' => __('validation.attributes')['users.extra.contact_id'],
                    ]));
                }
            }
        });
    }
}
