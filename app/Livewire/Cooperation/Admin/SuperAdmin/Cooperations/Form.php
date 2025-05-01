<?php

namespace App\Livewire\Cooperation\Admin\SuperAdmin\Cooperations;

use App\Enums\Country;
use App\Helpers\HoomdossierSession;
use App\Models\Cooperation;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;

class Form extends Component
{
    use AuthorizesRequests;

    public ?Cooperation $cooperationToEdit;
    public bool $clearApiKey = false;
    public bool $hasApiKey = false;

    public array $cooperationToEditFormData = [
        'name' => null,
        'slug' => null,
        'cooperation_email' => null,
        'website_url' => null,
        'econobis_wildcard' => null,
        'econobis_api_key' => null,
    ];

    protected function rules(): array
    {
        $slugUnique = Rule::unique('cooperations', 'slug');

        if ($this->cooperationToEdit->exists) {
            $slugUnique->ignore($this->cooperationToEdit->id);
        }

        return [
            'cooperationToEditFormData.name' => 'required',
            'cooperationToEditFormData.slug' => ['required', $slugUnique],
            'cooperationToEditFormData.country' => ['required', Rule::in(Country::cases())],
            'cooperationToEditFormData.cooperation_email' => ['nullable', 'email'],
            'cooperationToEditFormData.website_url' => ['nullable', 'url'],
            'cooperationToEditFormData.econobis_wildcard' => 'nullable',
            'cooperationToEditFormData.econobis_api_key' => ['nullable', 'string'],
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'cooperationToEditFormData.name' => 'Naam van de coöperatie',
            'cooperationToEditFormData.slug' => 'Slug / subdomein',
            'cooperationToEditFormData.cooperation_email' => 'Coöperatie contact e-mailadres',
            'cooperationToEditFormData.website_url' => 'Website URL',
            'cooperationToEditFormData.econobis_wildcard' => 'Econobis Domein Wildcard',
            'cooperationToEditFormData.econobis_api_key' => 'Econobis API key toevoegen',
        ];
    }

    public function mount(?Cooperation $cooperationToEdit = null): void
    {
        $this->cooperationToEdit = $cooperationToEdit;
        if ($cooperationToEdit->exists) {
            $this->fill([
                'cooperationToEditFormData' => $cooperationToEdit->only([
                    'name',
                    'slug',
                    'country',
                    'website_url',
                    'cooperation_email',
                    'econobis_wildcard',
                ]),
            ]);
            $this->hasApiKey = ! is_null($cooperationToEdit->econobis_api_key);
        }

        // Ensure set with default
        if (empty($this->cooperationToEditFormData['country'])) {
            $this->cooperationToEditFormData['country'] = Country::COUNTRY_NL;
        }
    }

    public function render(): View
    {
        return view('livewire.cooperation.admin.super-admin.cooperations.form');
    }

    public function updated(string $field, mixed $value): void
    {
        if ($field === "cooperationToEditFormData.slug") {
            $this->fill([$field => Str::of($value)->slug()->toString()]);
        }
    }

    public function updatedClearApiKey($shouldClear): void
    {
        if ($shouldClear) {
            $this->cooperationToEditFormData['econobis_api_key'] = null;
        }
    }

    public function slugify(): void
    {
        if (empty($this->cooperationToEditFormData['slug'] ?? [])) {
            $this->fill([
                'cooperationToEditFormData.slug' => Str::slug($this->cooperationToEditFormData['name'] ?? ''),
            ]);
        }
    }

    public function save(): Redirector|RedirectResponse
    {
        $validatedData = $this->validate();

        // We don't want this to be overridden since it could seriously damage the application.
        if ($this->cooperationToEdit->exists) {
            $validatedData['cooperationToEditFormData']['country'] = $this->cooperationToEdit->country ?? Country::COUNTRY_NL;
        }

        // just to be sure.
        $validatedData['cooperationToEditFormData']['slug'] = Str::slug($validatedData['cooperationToEditFormData']['slug']);
        $cooperationToEditFormData = $validatedData['cooperationToEditFormData'];

        // when you can create, you can update.
        $authAbility = $this->cooperationToEdit instanceof Cooperation && $this->cooperationToEdit->exists ? 'update' : 'create';
        $authArg = match ($authAbility) {
            'update' => $this->cooperationToEdit,
            'create' => Cooperation::class,
        };

        $this->authorize($authAbility, $authArg);

        // prev update method
        if ($this->clearApiKey) {
            $cooperationToEditFormData['econobis_api_key'] = null;
        } else {
            if (! empty($cooperationToEditFormData['econobis_api_key'])) {
                $cooperationToEditFormData['econobis_api_key'] = Crypt::encrypt($cooperationToEditFormData['econobis_api_key']);
            } else {
                // If it's empty we want to unset it, because we don't want to nullify the API key.
                unset($cooperationToEditFormData['econobis_api_key']);
            }
        }

        if ($this->cooperationToEdit->exists) {
            $message = __('cooperation/admin/super-admin/cooperations.update.success');
            $this->cooperationToEdit->update($cooperationToEditFormData);
        } else {
            $message = __('cooperation/admin/super-admin/cooperations.store.success');
            Cooperation::create($cooperationToEditFormData);
        }

        return redirect()
            ->route(
                'cooperation.admin.super-admin.cooperations.index',
                ['cooperation' => HoomdossierSession::getCooperation(true)]
            )
            ->with('success', $message);
    }
}
