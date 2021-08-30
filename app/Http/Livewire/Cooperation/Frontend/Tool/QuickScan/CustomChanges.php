<?php

namespace App\Http\Livewire\Cooperation\Frontend\Tool\QuickScan;

use App\Helpers\HoomdossierSession;
use App\Helpers\NumberFormatter;
use App\Models\CustomMeasureApplication;
use App\Models\InputSource;
use App\Models\UserActionPlanAdvice;
use App\Scopes\VisibleScope;
use Illuminate\Support\Str;
use Livewire\Component;

class CustomChanges extends Component
{
    public $customMeasureApplicationsFormData;
    public $cooperationMeasureApplications;

    public $masterInputSource;
    public $currentInputSource;

    public $cooperation;
    public $building;

    protected array $rules = [
        'customMeasureApplicationsFormData.*.name' => 'required',
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

    public function save($index)
    {
        $measure = $this->customMeasureApplicationsFormData[$index] ?? null;

        // We don't need to save each and every one every time one is saved, so we save by index
        if (! empty($measure)) {
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
            $costs['from'] = NumberFormatter::mathableFormat($costs['from'] ?? '', 2);
            $costs['to'] = NumberFormatter::mathableFormat($costs['to'] ?? '', 2);
            $this->customMeasureApplicationsFormData[$index]['costs'] = $costs;

            $measureData = $this->validate($customRules, [], $customAttributes);

            // Set update data for user action plan advice
            $updateData = [
                'category' => 'to-do',
                'costs' => $measure['costs'] ?? null,
                'input_source_id' => $this->currentInputSource->id,
                'savings_money' => $measure['savings_money'] ?? null,
            ];

            // If a hash and ID are set, then a measure has been edited
            if (! is_null($measure['hash']) && ! is_null($measure['id'])) {
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
                            'name' => ['nl' => $measure['name']]
                        ],
                    );
                }
            } else {
                $hash = Str::uuid();

                $customMeasureApplication = CustomMeasureApplication::create([
                    'building_id' => $this->building->id,
                    'input_source_id' => $this->currentInputSource->id,
                    'name' => ['nl' => $measure['name']],
                    'hash' => $hash
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
        $customMeasureApplications = $this->building->customMeasureApplications()->forInputSource($this->masterInputSource)->get();
        // Retrieve the cooperation's custom measures
        // TODO: Seeder not yet set, WIP
        $cooperationMeasureApplications = $this->cooperation->cooperationMeasureApplications;

        /** @var CustomMeasureApplication $customMeasureApplication */
        foreach ($customMeasureApplications as $index => $customMeasureApplication) {
            $this->customMeasureApplicationsFormData[$index] = $customMeasureApplication->only(['id', 'hash', 'name']);
            $this->customMeasureApplicationsFormData[$index]['extra'] = ['icon' => 'icon-tools'];

            $userActionPlanAdvice = $customMeasureApplication->userActionPlanAdvices()->forInputSource($this->masterInputSource)->first();

            $costs = $userActionPlanAdvice->costs;
            $this->customMeasureApplicationsFormData[$index]['costs'] = [
                'from' => NumberFormatter::format($costs['from'] ?? '', 1),
                'to' => NumberFormatter::format($costs['to'] ?? '', 1),
            ];

            $this->customMeasureApplicationsFormData[$index]['savings_money'] = NumberFormatter::format($userActionPlanAdvice->savings_money, 1);
        }

        // Append the option to add a new application
        $this->customMeasureApplicationsFormData[] = [
            'id' => null,
            'hash' => null,
            'name' => null,
            'costs' => [
                'from' => null,
                'to' => null,
            ],
            'savings_money' => null,
            'extra' => ['icon' => 'icon-tools'],
        ];
    }
}
