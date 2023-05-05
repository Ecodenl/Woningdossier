<?php

namespace App\Http\Livewire\Cooperation\Admin\SuperAdmin\Cooperations;

use App\Helpers\HoomdossierSession;
use App\Models\Cooperation;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Form extends Component
{
    use AuthorizesRequests;

    public $cooperationToEdit;

    public array $cooperationToEditFormData = [
        'slug' => null,
    ];

    public function mount(?Cooperation $cooperationToEdit = null)
    {
        $this->cooperationToEdit = $cooperationToEdit;
        if ($cooperationToEdit->exists) {
            $this->fill([
                'cooperationToEditFormData' => $cooperationToEdit->only(['name', 'slug', 'website_url', 'cooperation_email'])
            ]);
        }
    }

    public function updated($name, $value)
    {
        if ($name === "cooperationToEditFormData.slug") {
            $this->fill([$name => Str::of($value)->slug()]);
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
            'cooperationToEditFormData.cooperation_email' => ['nullable', 'email'],
            'cooperationToEditFormData.website_url' => ['nullable', 'url'],
        ];
    }

    public function save()
    {
        $validatedData = $this->validate();
        // just to be sure.
        $validatedData['cooperationToEditFormData']['slug'] = Str::slug($validatedData['cooperationToEditFormData']['slug']);
        // when you can create, you can update.
        $this->authorize('updateOrCreate', Cooperation::class);
        if ($this->cooperationToEdit->exists) {
            $this->cooperationToEdit->update($validatedData['cooperationToEditFormData']);
        } else {
            Cooperation::create($validatedData['cooperationToEditFormData']);
        }
        return redirect()
            ->route('cooperation.admin.super-admin.cooperations.index', ['cooperation' => HoomdossierSession::getCooperation(true)])
            ->with('success',  __('woningdossier.cooperation.admin.super-admin.cooperations.update.success'));
    }
    public function render()
    {
        return view('livewire.cooperation.admin.super-admin.cooperations.form');
    }
}
