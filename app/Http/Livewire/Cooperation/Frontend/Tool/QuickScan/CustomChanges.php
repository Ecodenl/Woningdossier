<?php

namespace App\Http\Livewire\Cooperation\Frontend\Tool\QuickScan;

use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Models\CustomMeasureApplication;
use App\Models\InputSource;
use App\Models\UserActionPlanAdvice;
use Cassandra\Custom;
use Illuminate\Support\Facades\Log;
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

    public function save()
    {
        foreach ($this->customMeasureApplicationsFormData as $customMeasureApplicationFormData) {

            if (!is_null($customMeasureApplicationFormData['hash'])) {
                /** @var CustomMeasureApplication $customMeasureApplication */
                $customMeasureApplication = CustomMeasureApplication::forInputSource($this->currentInputSource)
                    ->where('hash', $customMeasureApplicationFormData['hash'])
                    ->first();
                //todo: authorize the id, maybe the user did some arbitrage on the data

                $customMeasureApplication->update(['name' => ['nl' => $customMeasureApplicationFormData['name']]]);

            } else {
                if (!empty($customMeasureApplicationFormData['name'])) {
                    $hash = Str::uuid();

                    $customMeasureApplication = CustomMeasureApplication::create([
                        'building_id' => $this->building->id,
                        'input_source_id' => $this->currentInputSource->id,
                        'name' => ['nl' => $customMeasureApplicationFormData['name']],
                        'hash' => $hash
                    ]);
                }
            }
            // the default "voeg onderdeel toe" also holds data, but the name will be empty. So when name empty; do not save
            if (!empty($customMeasureApplicationFormData['name']) && (
                isset($customMeasureApplication) && $customMeasureApplication instanceof CustomMeasureApplication
            )) {

                $customMeasureApplication
                    ->userActionPlanAdvices()
                    ->allInputSources()
                    ->updateOrCreate(
                        [
                            'user_id' => $this->building->user->id,
                            'input_source_id' => $this->currentInputSource->id,
                        ],
                        [
                            'category' => 'to-do',
                            'costs' => $customMeasureApplicationFormData['costs'] ?? null,
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
        $customMeasureApplications = $this->building->customMeasureApplications()->forInputSource($this->masterInputSource)->get();
        $cooperationMeasureApplications = $this->cooperation->cooperationMeasureApplications;


        /** @var CustomMeasureApplication $customMeasureApplication */
        foreach ($customMeasureApplications as $index => $customMeasureApplication) {
            $this->customMeasureApplicationsFormData[$index] = $customMeasureApplication->only(['hash', 'name']);
            $this->customMeasureApplicationsFormData[$index]['extra'] = ['icon' => 'icon-tools'];

//            dd($customMeasureApplication->userActionPlanAdvices()->forInputSource($this->masterInputSource)->get(), $customMeasureApplication);
            $userActionPlanAdvice = $customMeasureApplication->userActionPlanAdvices()->forInputSource($this->masterInputSource)->first();
//            dd();
            if ($userActionPlanAdvice instanceof UserActionPlanAdvice) {
                $this->customMeasureApplicationsFormData[$index]['costs'] = $userActionPlanAdvice->costs;
            }
        }

//        foreach ($cooperationMeasureApplications as $cooperationMeasureApplication) {
//            $this->cooperationMeasureApplications[] = $cooperationMeasureApplication->only(['id', 'name', 'info', 'extra']);
//        }

        $this->customMeasureApplicationsFormData[] = [
            'hash' => null,
            'name' => null,
            'extra' => ['icon' => 'icon-tools']
        ];
    }
}
