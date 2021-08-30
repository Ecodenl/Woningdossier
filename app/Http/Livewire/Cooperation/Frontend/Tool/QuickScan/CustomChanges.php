<?php

namespace App\Http\Livewire\Cooperation\Frontend\Tool\QuickScan;

use App\Helpers\HoomdossierSession;
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

    public function mount()
    {
        $this->building = HoomdossierSession::getBuilding(true);
        $this->masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
        $this->currentInputSource = HoomdossierSession::getInputSource(true);
        $this->cooperation = HoomdossierSession::getCooperation(true);

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
                    // input source. We also won't update if the name is empty
                    if (! empty($measure['name'])) {
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

                }
            } else {
                if (! empty($measure['name'])) {
                    $hash = Str::uuid();

                    $customMeasureApplication = CustomMeasureApplication::create([
                        'building_id' => $this->building->id,
                        'input_source_id' => $this->currentInputSource->id,
                        'name' => ['nl' => $measure['name']],
                        'hash' => $hash
                    ]);
                }
            }

            // The default "voeg onderdeel toe" also holds data, but the name will be empty. So when name empty; do not save
            if (! empty($measure['name']) && (
                isset($customMeasureApplication) && $customMeasureApplication instanceof CustomMeasureApplication
            )) {
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
                        [
                            'category' => 'to-do',
                            'costs' => $measure['costs'] ?? null,
                            'input_source_id' => $this->currentInputSource->id
                        ]
                    );
            }
        }

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
            $this->customMeasureApplicationsFormData[$index]['costs'] = $userActionPlanAdvice->costs;
        }

        // Append the option to add a new application
        $this->customMeasureApplicationsFormData[] = [
            'id' => null,
            'hash' => null,
            'name' => null,
            'extra' => ['icon' => 'icon-tools']
        ];
    }
}
