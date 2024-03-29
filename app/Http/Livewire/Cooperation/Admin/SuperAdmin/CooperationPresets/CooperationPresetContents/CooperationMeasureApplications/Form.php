<?php

namespace App\Http\Livewire\Cooperation\Admin\SuperAdmin\CooperationPresets\CooperationPresetContents\CooperationMeasureApplications;

use App\Helpers\HoomdossierSession;
use App\Models\CooperationPreset;
use App\Models\CooperationPresetContent;
use App\Models\MeasureCategory;
use App\Rules\LanguageRequired;
use Illuminate\Support\Collection;
use Livewire\Component;

class Form extends Component
{
    public CooperationPreset $cooperationPreset;
    public ?CooperationPresetContent $cooperationPresetContent;
    public Collection $measures;

    public array $content = [
        // Required defaults
        'name' => [
            'nl' => '',
        ],
        'info' => [
            'nl' => '',
        ],
        'is_extensive_measure' => false,
    ];

    protected function rules(): array
    {
        $evaluateGt = ! empty($this->content['costs']['from']);

        return  [
            'content.name' => ['required', new LanguageRequired()],
            'content.info' => ['required', new LanguageRequired()],
            'content.relations.mapping.measure_category' => [
                'nullable', 'exists:measure_categories,id',
            ],
            'content.costs.from' => [
                'nullable', 'numeric', 'min:0',
            ],
            'content.costs.to' => [
                'required', 'numeric', 'min:0', $evaluateGt ? 'gt:content.costs.from' : '',
            ],
            'content.savings_money' => [
                'required', 'numeric', 'min:0',
            ],
            'content.extra.icon' => [
                'required',
            ],
            'content.is_extensive_measure' => [
                'required',
                'boolean',
            ],
        ];
    }

    protected $listeners = [
        'fieldUpdate',
    ];

    public function mount(?CooperationPresetContent $cooperationPresetContent = null)
    {
        // Normally we can let Livewire set the model for us, however, on create we don't have one. Yet, by casting
        // a model as null, we get a fresh model object, on which we can call things such as ->exists.
        $this->cooperationPresetContent = $cooperationPresetContent;
        if ($cooperationPresetContent->exists) {
            $this->fill([
                'content' => $cooperationPresetContent->content,
            ]);
        }

        $this->measures = MeasureCategory::all();
    }

    public function render()
    {
        return view('livewire.cooperation.admin.super-admin.cooperation-presets.cooperation-preset-contents.cooperation-measure-applications.form');
    }

    public function fieldUpdate($field, $value)
    {
        $prop = $this->beforeFirstDot($field);

        if ($this->containsDots($field)) {
            $key = $this->afterFirstDot($field);
            data_set($this->{$prop}, $key, $value);
        } else {
            $this->{$prop} = $value;
        }
    }

    public function save()
    {
        $content = $this->validate()['content'];
        $content['is_deletable'] = ! $content['is_extensive_measure'];
        $category = $content['relations']['mapping']['measure_category'] ?? null;
        if ($content['is_extensive_measure'] || is_null($category)) {
            unset($content['relations']['mapping']['measure_category']);
        }

        if ($this->cooperationPresetContent->exists) {
            $this->cooperationPresetContent->update(compact('content'));
            $message = __('cooperation/admin/super-admin/cooperation-preset-contents.update.success');
        } else {
            $this->cooperationPreset->cooperationPresetContents()->create(compact('content'));
            $message = __('cooperation/admin/super-admin/cooperation-preset-contents.store.success');
        }

        $cooperation = HoomdossierSession::getCooperation(true);
        return redirect()->route(
            'cooperation.admin.super-admin.cooperation-presets.show',
            ['cooperation' => $cooperation, 'cooperationPreset' => $this->cooperationPreset]
        )->with('success', $message);
    }
}
