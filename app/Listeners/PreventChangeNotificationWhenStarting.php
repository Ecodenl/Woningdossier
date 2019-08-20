<?php

namespace App\Listeners;

use App\Events\ExampleBuildingChanged;
use App\Events\StepDataHasBeenChanged;
use App\Helpers\Cache\Step;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\ToolSetting;
use App\Scopes\GetValueScope;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;

class PreventChangeNotificationWhenStarting
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     *
     * @return void
     */
    public function handle(ExampleBuildingChanged $event)
    {
        // check if we come from nothing
        if (is_null($event->from)){
            $this->suppressExampleBuildingNotification($event->building);
            return; // and exit here
        }

        // We check if the first step is filled, but the second step is not.
        // If so, the tool is filled for the first time and there is nothing
        // to compare, which makes a notification unwanted

        // Therefore: if the second step is not filled, we unset particular
        // notifications (in this case: of input source 'example building')

        /**
         * @var Collection $steps
         */
        $steps = Step::getOrdered();
        if ($steps->count() > 1) {
            // get next
            $second = $steps->get(1);
            if ( ! $event->building->hasCompleted($second)) {
                $this->suppressExampleBuildingNotification($event->building);
            }
        }
    }

    protected function suppressExampleBuildingNotification(Building $building){
        // second step was not completed yet: if a notification for
        // the input source 'example-building' is set, set it to false
        $inputSourceExampleBuilding = InputSource::findByShort('example-building');
        if ($inputSourceExampleBuilding instanceof InputSource) {
            // Explicitly update to false
            ToolSetting::withoutGlobalScope(GetValueScope::class)
                       ->where('building_id', '=', $building->id)
                       ->where('changed_input_source_id', '=',
                           $inputSourceExampleBuilding->id)
                       ->where('has_changed', '=', true)
                       ->update(['has_changed' => false]);

        }
    }
}
