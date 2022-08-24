<?php

namespace App\Http\Livewire\Cooperation\Frontend\Layouts\Parts;

use App\Helpers\Conditions\ConditionEvaluator;
use App\Models\Alert;
use App\Models\Building;
use App\Models\InputSource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Alerts extends Component
{
    protected $listeners = ['refreshAlerts'];

    public $alerts;
    public $building;
    public $inputSource;
    public $alertOpen = false;

    public $typeMap = [
        'info' => 'text-blue',
        'success' => 'text-green',
        'warning' => 'text-orange',
        'danger' => 'text-red',
    ];

    public function mount(Request $request, Building $building, InputSource $inputSource)
    {
        $this->fill(compact('building', 'inputSource'));

        if ($request->route()->getName() === 'cooperation.frontend.tool.quick-scan.my-plan.index') {
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
        $alerts = Alert::all();

        $oldAlerts = $this->alerts;

        $shouldOpenAlert = $this->alertOpen;

        foreach ($alerts as $index => $alert) {
            $condition = ConditionEvaluator::init()
                ->building($this->building)
                ->inputSource($this->inputSource)
                ->explain();

            // the issue here is that the collection is not correctly passsed
            if ($condition->evaluate($alert->conditions, collect($answers))) {
                $oldAlert = $oldAlerts->where('short', $alert->short)->first();
                // if the current alert is not found in the oldAlerts, it will be considered "new"
                // in that case we will open the alert for the user
                if(!$oldAlert instanceof Alert) {
                    $shouldOpenAlert = true;
                }
            } else  {
                Log::debug("Evaluation is false, forgetting {$alert->text}");
                $alerts->forget($index);
            }
        }

        if ($alerts->isEmpty()) {
            $shouldOpenAlert = false;
        }

        $this->alertOpen = $shouldOpenAlert;
        $this->alerts = $alerts;

        // Always call updated for this field. Even if it didn't change, we want to ensure it is cast to the frontend
        $this->updated('alertOpen', $shouldOpenAlert);
    }

    private function isClosed()
    {
        return !$this->alertOpen;
    }

    public function updated($field, $value)
    {
        $this->dispatchBrowserEvent('element:updated', ['field' => $field, 'value' => $value]);
    }
}
