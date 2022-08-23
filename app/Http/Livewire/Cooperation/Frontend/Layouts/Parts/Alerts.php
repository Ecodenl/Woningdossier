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
    public $shouldOpenAlert = false;

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
            $this->shouldOpenAlert = true;
        }
        $this->refreshAlerts();
    }

    public function render()
    {
        return view('livewire.cooperation.frontend.layouts.parts.alerts');
    }

    public function refreshAlerts()
    {
        Log::debug('refreshee');
        $alerts = Alert::all();
        foreach ($alerts as $alert) {
            $condition = ConditionEvaluator::init()
                ->building($this->building)
                ->inputSource($this->inputSource);

            // when the condition is NOT met, we will remove the alert from the collection
            // this way we have a collection of alerts that are are relevant for the current user
            if (!$condition->evaluate($alert->conditions ?? [])) {
                Log::debug($alert->id);
                $alerts->forget($alert->id);
            }
        }
        $this->alerts = $alerts;
    }
}
