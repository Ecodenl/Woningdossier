<?php

namespace App\Http\Livewire\Cooperation\Frontend\Tool\QuickScan;

use App\Helpers\HoomdossierSession;
use App\Helpers\NumberFormatter;
use App\Models\CooperationMeasureApplication;
use App\Models\CustomMeasureApplication;
use App\Models\InputSource;
use App\Models\UserActionPlanAdvice;
use App\Scopes\VisibleScope;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Livewire\Component;

class CustomChanges extends Component
{
    public $customMeasureApplicationsFormData;
    public $cooperationMeasureApplicationsFormData;
    public array $selectedCustomMeasureApplications = [];
    public array $selectedCooperationMeasureApplications = [];
    public array $previousSelectedState = [];

    public $masterInputSource;
    public $currentInputSource;

    public $cooperation;
    public $building;

    protected array $rules = [
        'customMeasureApplicationsFormData.*.name' => 'required',
        'customMeasureApplicationsFormData.*.info' => 'required',
        'customMeasureApplicationsFormData.*.costs.from' => 'required|numeric|min:0',
        'customMeasureApplicationsFormData.*.costs.to' => 'required|numeric|gt:customMeasureApplicationsFormData.*.costs.from',
        'customMeasureApplicationsFormData.*.savings_money' => 'nullable|numeric',
    ];

    public array $attributes;

    public function mount()
    {
        $this->building = HoomdossierSession::getBuilding(true);
        $this->masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
        $this->currentInputSource = HoomdossierSession::getInputSource(true);
        $this->cooperation = HoomdossierSession::getCooperation(true);

        // Do it like this because otherwise the translation doesn't work
        $globalAttributeTranslations = __('validation.attributes');

        $this->attributes = [
            'customMeasureApplicationsFormData.*.name' => $globalAttributeTranslations['custom_measure_application.name'],
            'customMeasureApplicationsFormData.*.info' => $globalAttributeTranslations['custom_measure_application.info'],
            'customMeasureApplicationsFormData.*.costs.from' => $globalAttributeTranslations['custom_measure_application.costs.from'],
            'customMeasureApplicationsFormData.*.costs.to' => $globalAttributeTranslations['custom_measure_application.costs.to'],
            'customMeasureApplicationsFormData.*.savings_money' => $globalAttributeTranslations['custom_measure_application.savings_money'],
        ];

        $this->setCustomMeasureApplications();
    }

    public function render()
    {
        return view('livewire.cooperation.frontend.tool.quick-scan.custom-changes');
    }

    public function updatedSelectedCooperationMeasureApplications($value)
    {
        abort_if(HoomdossierSession::isUserObserving(), 403);
        $key = 'cooperationMeasureApplications';

        // Let's diff with previous values, to define which index has changed
        $added = array_diff($value, $this->previousSelectedState[$key]);
        $removed = array_diff($this->previousSelectedState[$key], $value);

        $index = empty ($added) ? Arr::first($removed) : Arr::first($added);
        // If removed is not empty, it's not visible, if it is empty, it is visible
        $visible = empty ($removed);

        $measure = $this->cooperationMeasureApplicationsFormData[$index] ?? null;

        if (!empty($measure)) {
            $cooperationMeasureApplication = CooperationMeasureApplication::find($measure['id']);

            // No bogged data
            if ($cooperationMeasureApplication instanceof CooperationMeasureApplication) {
                // Make action plan advice for user, or update it, with the measure data and the set visibility
                $userActionPlanAdvice = $cooperationMeasureApplication->userActionPlanAdvices()
                    ->forInputSource($this->currentInputSource)
                    ->where('user_id', $this->building->user->id)
                    ->first();

                // We can't updateOrCreate, because we don't want to interfere with potential user
                // settings, e.g. category
                if ($userActionPlanAdvice instanceof UserActionPlanAdvice) {
                    $userActionPlanAdvice->update([
                        'visible' => $visible,
                    ]);
                } else {
                    $cooperationMeasureApplication->userActionPlanAdvices()
                        ->create([
                            'user_id' => $this->building->user->id,
                            'input_source_id' => $this->currentInputSource->id,
                            'category' => 'to-do',
                            'costs' => $cooperationMeasureApplication->costs,
                            'savings_money' => $cooperationMeasureApplication->savings_money,
                            'visible' => $visible,
                        ]);
                }
            }
        }

        // Update selected state
        $this->previousSelectedState[$key] = $this->selectedCooperationMeasureApplications;
    }

    public function updatedSelectedCustomMeasureApplications($value)
    {
        abort_if(HoomdossierSession::isUserObserving(), 403);

        $key = 'customMeasureApplications';

        // Let's diff with previous values, to define which index has changed
        $added = array_diff($value, $this->previousSelectedState[$key]);
        $removed = array_diff($this->previousSelectedState[$key], $value);

        $index = empty ($added) ? Arr::first($removed) : Arr::first($added);
        // If removed is not empty, it's not visible, if it is empty, it is visible
        $visible = empty ($removed);

        $measure = $this->customMeasureApplicationsFormData[$index] ?? null;

        if (!empty($measure)) {
            $masterCustomMeasureApplication = CustomMeasureApplication::forInputSource($this->masterInputSource)
                ->where('hash', $measure['hash'])
                ->where('id', $measure['id'])
                ->first();

            if ($masterCustomMeasureApplication instanceof CustomMeasureApplication) {
                $customMeasureApplication = $masterCustomMeasureApplication->getSibling($this->currentInputSource);

                // There is a chance the measure is from the coach, so if that's the case we will just update
                // the master input source
                if ($customMeasureApplication instanceof CustomMeasureApplication) {
                    $customMeasureApplication->userActionPlanAdvices()
                        ->forInputSource($this->currentInputSource)
                        ->first()
                        ->update([
                            'visible' => $visible,
                        ]);
                } else {
                    $masterCustomMeasureApplication->userActionPlanAdvices()
                        ->forInputSource($this->masterInputSource)
                        ->first()
                        ->update([
                            'visible' => $visible,
                        ]);
                }
            }
        }

        // Update selected state
        $this->previousSelectedState[$key] = $this->selectedCustomMeasureApplications;
    }

    public function save($index)
    {
        // unauth the user if this happens, this means the user is just messing around.
        abort_if(HoomdossierSession::isUserObserving(), 403);

        $measure = $this->customMeasureApplicationsFormData[$index] ?? null;

        // We don't need to save each and every one every time one is saved, so we save by index
        if (!empty($measure)) {
            // We must validate on index base, so we replace the wild card with the current index
            $customRules = [];
            foreach ($this->rules as $key => $rule) {
                $key = str_replace('*', $index, $key);
                $rule = str_replace('*', $index, $rule);

                $customRules[$key] = $rule;
            }
            $customAttributes = [];
            foreach ($this->attributes as $key => $translation) {
                $key = str_replace('*', $index, $key);

                $customAttributes[$key] = $translation;
            }

            // Before we can validate, we must convert human format to proper format
            $costs = $measure['costs'] ?? [];
            $costs['from'] = NumberFormatter::mathableFormat(str_replace('.', '', $costs['from'] ?? ''), 2);
            $costs['to'] = NumberFormatter::mathableFormat(str_replace('.', '', $costs['to'] ?? ''), 2);
            $this->customMeasureApplicationsFormData[$index]['costs'] = $costs;
            $this->customMeasureApplicationsFormData[$index]['savings_money'] = NumberFormatter::mathableFormat(str_replace('.', '', $measure['savings_money'] ?? 0), 2);

            $measureData = $this->validate($customRules, [], $customAttributes);

            // It validated, let's re-fetch the measure so the values are correct
            $measure = $this->customMeasureApplicationsFormData[$index];

            // Set update data for user action plan advice
            $updateData = [
                'category' => 'to-do',
                'costs' => $measure['costs'] ?? null,
                'input_source_id' => $this->currentInputSource->id,
                'savings_money' => $measure['savings_money'] ?? 0,
            ];

            // If a hash and ID are set, then a measure has been edited
            if (!is_null($measure['hash']) && !is_null($measure['id'])) {
                // ID is set for master input source, so we fetch the master input source custom measure
                /** @var CustomMeasureApplication $customMeasureApplication */
                $masterCustomMeasureApplication = CustomMeasureApplication::forInputSource($this->masterInputSource)
                    ->where('hash', $measure['hash'])
                    ->where('id', $measure['id'])
                    ->first();

                // If it's not instanceof, something was borked by the user
                if ($masterCustomMeasureApplication instanceof CustomMeasureApplication) {
                    // The measure might be from the coach. We updateOrCreate to ensure it gets added to our own
                    // input source.
                    $customMeasureApplication = CustomMeasureApplication::updateOrCreate(
                        [
                            'building_id' => $this->building->id,
                            'input_source_id' => $this->currentInputSource->id,
                            'hash' => $masterCustomMeasureApplication->hash,
                        ],
                        [
                            'name' => ['nl' => $measure['name']],
                            'info' => ['nl' => $measure['info']],
                        ],
                    );
                }
            } else {
                $hash = Str::uuid();

                $customMeasureApplication = CustomMeasureApplication::create([
                    'building_id' => $this->building->id,
                    'input_source_id' => $this->currentInputSource->id,
                    'name' => ['nl' => $measure['name']],
                    'info' => ['nl' => $measure['info']],
                    'hash' => $hash,
                ]);

                $updateData['visible'] = true;
            }

            // The default "voeg onderdeel toe" also holds data, but the name will be empty. So when name empty; do not save
            if (isset($customMeasureApplication) && $customMeasureApplication instanceof CustomMeasureApplication) {
                // Update the user action plan advice linked to this custom measure
                $customMeasureApplication
                    ->userActionPlanAdvices()
                    ->allInputSources()
                    ->withoutGlobalScope(VisibleScope::class)
                    ->updateOrCreate(
                        [
                            'user_id' => $this->building->user->id,
                            'input_source_id' => $this->currentInputSource->id,
                        ],
                        $updateData
                    );
            }
        }

        $this->dispatchBrowserEvent('close-modal');

        $this->setCustomMeasureApplications();
    }

    private function setCustomMeasureApplications()
    {
        $this->customMeasureApplicationsFormData = [];
        // Retrieve the user's custom measures
        $customMeasureApplications = $this->building->customMeasureApplications()
            ->forInputSource($this->masterInputSource)->get();

        $this->cooperationMeasureApplicationsFormData = [];
        // Retrieve the cooperation's custom measures
        $cooperationMeasureApplications = $this->cooperation->cooperationMeasureApplications;

        // Set the cooperation measures
        /** @var \App\Models\CooperationMeasureApplication $cooperationMeasureApplication */
        foreach ($cooperationMeasureApplications as $index => $cooperationMeasureApplication) {
            $this->cooperationMeasureApplicationsFormData[$index] = $cooperationMeasureApplication->only(['id', 'name', 'extra']);

            $costs = $cooperationMeasureApplication->costs;
            $this->cooperationMeasureApplicationsFormData[$index]['costs'] = [
                'from' => NumberFormatter::format($costs['from'] ?? '', 1),
                'to' => NumberFormatter::format($costs['to'] ?? '', 1),
            ];

            $this->cooperationMeasureApplicationsFormData[$index]['savings_money'] = NumberFormatter::format($cooperationMeasureApplication->savings_money, 1);

            // Let's see if a userActionPlanAdvice exists, so we know if it should be checked
            $userActionPlanAdvice = $cooperationMeasureApplication->userActionPlanAdvices()
                ->forInputSource($this->masterInputSource)
                ->where('user_id', $this->building->user->id)
                ->first();

            if ($userActionPlanAdvice instanceof UserActionPlanAdvice && $userActionPlanAdvice->visible) {
                $this->selectedCooperationMeasureApplications[] = (string)$index;
            }
        }

        // Set the custom measures
        /** @var CustomMeasureApplication $customMeasureApplication */
        foreach ($customMeasureApplications as $index => $customMeasureApplication) {
            $this->customMeasureApplicationsFormData[$index] = $customMeasureApplication->only(['id', 'hash', 'name', 'info',]);
            $this->customMeasureApplicationsFormData[$index]['extra'] = ['icon' => 'icon-tools'];

            $userActionPlanAdvice = $customMeasureApplication->userActionPlanAdvices()
                ->forInputSource($this->masterInputSource)
                ->first();

            if ($userActionPlanAdvice instanceof UserActionPlanAdvice) {

                $this->customMeasureApplicationsFormData[$index]['costs'] = [
                    'from' => NumberFormatter::format($costs['from'] ?? '', 1),
                    'to' => NumberFormatter::format($costs['to'] ?? '', 1),
                ];

                $this->customMeasureApplicationsFormData[$index]['savings_money'] = NumberFormatter::format($userActionPlanAdvice->savings_money, 1);

                if ($userActionPlanAdvice->visible) {
                    $this->selectedCustomMeasureApplications[] = (string)$index;
                }
            }
        }

        // Append the option to add a new application
        $this->customMeasureApplicationsFormData[] = [
            'id' => null,
            'hash' => null,
            'name' => null,
            'info' => null,
            'costs' => [
                'from' => null,
                'to' => null,
            ],
            'savings_money' => null,
            'extra' => ['icon' => 'icon-tools'],
        ];

        // We're done, let's define our selected state
        $this->previousSelectedState = [
            'customMeasureApplications' => $this->selectedCustomMeasureApplications,
            'cooperationMeasureApplications' => $this->selectedCooperationMeasureApplications,
        ];
    }
}