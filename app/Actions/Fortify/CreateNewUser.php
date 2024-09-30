<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Helpers\RoleHelper;
use App\Http\Requests\AddressFormRequest;
use App\Models\Account;
use App\Rules\PhoneNumber;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    protected array $input = [];
    protected ?Request $request = null;

    public function request(Request $request): self
    {
        $this->request = $request;
        return $this;
    }

    /**
     * Validate and create a newly registered user.
     */
    public function create(array $input): User
    {
        $this->request ??= request();
        $this->input = $input;
        $this->prepareForValidation();

        Validator::make($input, $this->rules(), $this->messages())->validate();

        $cooperation = $this->request->route('cooperation');

        return UserService::register($cooperation, [RoleHelper::ROLE_RESIDENT], $input);
    }

    private function rules(): array
    {
        $rules = array_merge([
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(Account::class),
            ],
            'password' => $this->passwordRules(),
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_number' => ['nullable', new PhoneNumber('nl')],
            'allow_access' => 'required|accepted',
        ], (new AddressFormRequest())->rules());

        // try to get the account
        $account = Account::where('email', $this->get('email'))->first();
        // if the account exists but the user is not associated with the current cooperation
        // then we unset the email and password rule because we dont need to validate them, we handle them in the controller
        if ($account instanceof Account && ! $account->isAssociatedWith($this->request->route('cooperation'))) {
            unset($rules['email'], $rules['password']);
        }

        return $rules;
    }

    private function messages()
    {
        return [
            'allow_access.required' => __('auth.register.validation.allow_access'),
        ];
    }

    /**
     * so fields can be modified or added before validation.
     */
    private function prepareForValidation()
    {
        // Add new data field before it gets sent to the validator
        $this->set([
            'extension' => strtolower(preg_replace("/[\s-]+/", '', $this->get('extension', ''))),
        ]);
    }

    // Getter and setter for input to make code easier to read
    private function get(string $key, $default = null)
    {
        return data_get($this->input, $key, $default);
    }

    private function set($key, $value = null)
    {
        if (! is_array($key)) {
            $key = [$key => $value];
        }

        foreach ($key as $arrayKey => $arrayValue) {
            data_set($this->input, $arrayKey, $arrayValue);
        }
    }
}
