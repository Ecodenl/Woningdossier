<?php

namespace App\Http\Livewire\Cooperation\Admin\SuperAdmin\Cooperations;

use App\Helpers\HoomdossierSession;
use App\Models\Cooperation;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Form extends Component
{
    use AuthorizesRequests;

    public $cooperationToEdit;
    public bool $clearApiKey = false;
    public bool $hasApiKey = false;

    public array $cooperationToEditFormData = [
        'slug' => null,
    ];

    public function mount(?Cooperation $cooperationToEdit = null)
    {
        $this->cooperationToEdit = $cooperationToEdit;
        if ($cooperationToEdit->exists) {
            $this->fill([
                'cooperationToEditFormData' => $cooperationToEdit->only([
                    'name',
                    'slug',
                    'website_url',
                    'cooperationToEdit_email',
                    'econobis_wildcard'
                ])
            ]);
            $this->hasApiKey = ! is_null($cooperationToEdit->econobis_api_key);
        }
    }

    public function updated($name, $value)
    {
        if ($name === "cooperationToEditFormData.slug") {
            $this->fill([$name => Str::of($value)->slug()]);
        }
    }

    public function updatedClearApiKey($shouldClear)
    {
        if ($shouldClear) {
            $this->cooperationToEditFormData['econobis_api_key'] = null;
        }
    }

    public function slugify()
    {
        if (empty($this->cooperationToEditFormData['slug'] ?? [])) {
            $this->fill(['cooperationToEditFormData.slug' => Str::slug($this->cooperationToEditFormData['name'])]);
        }
    }

    public function rules()
    {
        $slugUnique = Rule::unique('cooperations', 'slug');
        if ($this->cooperationToEdit->exists) {
            $slugUnique->ignore($this->cooperationToEdit->id);
        }
        return [
            'cooperationToEditFormData.name' => 'required',
            'cooperationToEditFormData.slug' => ['required', $slugUnique],
            'cooperationToEditFormData.website_url' => 'nullable|url',
            'cooperationToEditFormData.cooperation_email' => 'nullable|email',
            'cooperationToEditFormData.econobis_wildcard' => 'nullable',
            'cooperationToEditFormData.econobis_api_key' => ['nullable', 'string'],

        ];
    }

    public function save()
    {
        $validatedData = $this->validate();
        // just to be sure.
        $validatedData['cooperationToEditFormData']['slug'] = Str::slug($validatedData['cooperationToEditFormData']['slug']);
        $cooperationToEditFormData = $validatedData['cooperationToEditFormData'];
        // when you can create, you can update.
        $this->authorize('updateOrCreate', Cooperation::class);

        // prev update method
        if ($this->clearApiKey) {
            $cooperationToEditFormData['econobis_api_key'] = null;
        } else {
            if ( ! empty($cooperationToEditFormData['econobis_api_key'])) {
                $cooperationToEditFormData['econobis_api_key'] = Crypt::encrypt($cooperationToEditFormData['econobis_api_key']);
            } else {
                // If it's empty we want to unset it, because we don't want to nullify the API key.
                unset($cooperationToEditFormData['econobis_api_key']);
            }
        }


        if ($this->cooperationToEdit->exists) {
            $this->cooperationToEdit->update($cooperationToEditFormData);
        } else {
            Cooperation::create($cooperationToEditFormData);
        }
        return redirect()
            ->route('cooperation.admin.super-admin.cooperations.index',
                ['cooperation' => HoomdossierSession::getCooperation(true)])
            ->with('success', __('woningdossier.cooperation.admin.super-admin.cooperations.update.success'));
    }

    public function render()
    {
        return view('livewire.cooperation.admin.super-admin.cooperations.form');
    }
}
