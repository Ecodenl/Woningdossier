<?php

namespace App\Http\Livewire\Cooperation\Frontend\Tool\QuickScan;

use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Models\CustomMeasureApplication;
use App\Models\InputSource;
use Livewire\Component;

class CustomChanges extends Component
{
    public $customMeasureApplications;
    public $masterInputSource;
    public $building;

    public function mount()
    {
        $this->building = HoomdossierSession::getBuilding(true);
        $this->masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);

        $this->setCustomMeasureApplications();

    }

    public function render()
    {
        return view('livewire.cooperation.frontend.tool.quick-scan.custom-changes');
    }

    public function save()
    {
        foreach ($this->customMeasureApplications as $customMeasureApplication) {

            if (!is_null($customMeasureApplication['id'])) {
                CustomMeasureApplication::where('id', $customMeasureApplication['id'])
                    ->where('building_id', $this->building->id)
                    ->where('input_source_id', $this->masterInputSource->id)
                    ->update($customMeasureApplication);
            } else {

                CustomMeasureApplication::create(array_merge([
                    'building_id' => $this->building->id,
                    'input_source_id' => $this->masterInputSource->id,

                ], $customMeasureApplication));
            }
        }

        $this->setCustomMeasureApplications();
    }

    private function setCustomMeasureApplications()
    {
        $this->customMeasureApplications = [];
        $customMeasureApplications = $this->building->customMeasureApplications()->forInputSource($this->masterInputSource)->get();
        /** @var CustomMeasureApplication $customMeasureApplication */
        foreach ($customMeasureApplications as $customMeasureApplication) {
            $this->customMeasureApplications[] = $customMeasureApplication->only(['id', 'name', 'info', 'extra']);
        }

        $this->customMeasureApplications[] = [
            'id' => null,
            'name' => null,
            'info' => null,
            'extra' => ['icon' => 'icon-tools']
        ];
    }
}
