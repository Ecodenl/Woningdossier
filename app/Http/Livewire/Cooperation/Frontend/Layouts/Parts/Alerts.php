<?php

namespace App\Http\Livewire\Cooperation\Frontend\Layouts\Parts;

use App\Helpers\Conditions\ConditionEvaluator;
use App\Models\Alert;
use App\Models\Building;
use App\Models\InputSource;
use App\Services\Models\AlertService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Livewire\Component;

class Alerts extends Component
{
    protected $listeners = ['refreshAlerts'];

    public Collection $alerts;
    public Building $building;
    public InputSource $inputSource;
    public bool $alertOpen = false;

    // Used in the blade view
    public array $typeMap = AlertService::TYPE_MAP;

    public function mount(Request $request, Building $building, InputSource $inputSource)
    {
        // Default so it's callable.
        $this->alerts = collect();
        $this->fill(compact('building', 'inputSource'));

        if ($request->route()->getName() === 'cooperation.frontend.tool.simple-scan.my-plan.index') {
            $this->alertOpen = true;
        }
        $this->refreshAlerts();
    }

    public function render()
    {
        return view('livewire.cooperation.frontend.layouts.parts.alerts');
    }

    public function refreshAlerts($answers = null)
    {
        $oldAlerts = $this->alerts;
        $shouldOpenAlert = false;

        $newAlerts = AlertService::init()
            ->inputSource($this->inputSource)
            ->building($this->building)
            ->setAnswers(collect($answers))
            ->getAlerts();

        if (! empty($newAlerts)) {
            $oldAlertShorts = $oldAlerts->pluck('short')->toArray();
            $newAlertShorts = $newAlerts->pluck('short')->toArray();

            $shouldOpenAlert = ! empty(array_diff($newAlertShorts, $oldAlertShorts));
        }

        $this->alertOpen = $shouldOpenAlert;
        $this->alerts = $newAlerts;

        // Always call updated for this field. Even if it didn't change, we want to ensure it is cast to the frontend
        $this->updated('alertOpen', $shouldOpenAlert);
    }

    public function updated($field, $value)
    {
        $this->dispatchBrowserEvent('element:updated', ['field' => $field, 'value' => $value]);
    }
}
