<?php

namespace App\Http\Livewire\Cooperation\Frontend\Layouts\Parts;

use App\Helpers\Conditions\ConditionEvaluator;
use App\Models\Alert;
use App\Models\Building;
use App\Models\InputSource;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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
        Alert::TYPE_INFO => 'text-blue',
        Alert::TYPE_SUCCESS => 'text-green',
        Alert::TYPE_WARNING => 'text-orange',
        Alert::TYPE_DANGER => 'text-red',
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

        $shouldOpenAlert = false;

        $evaluator = ConditionEvaluator::init()
            ->building($this->building)
            ->inputSource($this->inputSource);

        foreach ($alerts as $index => $alert) {
            // Get answers and merge any potential new answers
            $answersForAlert = $evaluator->getToolAnswersForConditions($alert->conditions)->merge(collect($answers));

            // Check if we should show this alert
            if ($evaluator->evaluateCollection($alert->conditions, $answersForAlert)) {
                $oldAlert = null;
                if ($oldAlerts instanceof Collection) {
                    $oldAlert = $oldAlerts->where('short', $alert->short)->first();
                }
                // if the current alert is not found in the oldAlerts, it will be considered "new"
                // in that case we will open the alert for the user
                if(! $oldAlert instanceof Alert) {
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
