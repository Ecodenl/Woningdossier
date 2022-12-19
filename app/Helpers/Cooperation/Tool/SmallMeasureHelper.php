<?php

namespace App\Helpers\Cooperation\Tool;

use App\Models\MeasureApplication;
use App\Models\Step;
use App\Models\UserActionPlanAdvice;
use App\Services\UserActionPlanAdviceService;

class SmallMeasureHelper extends ToolHelper
{
    // All the answers linked to each measure
    const MEASURE_QUESTION_LINK = [
        'save-energy-with-light' => [
            'apply-led-light', 'turn-off-lights',
        ],
        'energy-efficient-equipment' => [
            'replace-old-equipment', 'turn-off-unused-equipment', 'turn-off-standby-equipment',
            'only-use-full-washing-machine', 'only-use-full-tumble-dryer',
        ],
        'energy-efficient-installations' => [
            'pump-switch-floor-heating', 'replace-alternating-current-fan',
        ],
        'save-energy-with-crack-sealing' => [
            'crack-sealing-windows-doors', 'mailbox-bristles',
        ],
        'improve-radiators' => [
            'radiator-foil', 'no-curtains-for-the-radiator', 'apply-thermostat-knobs',
            'apply-radiator-ventilation', 'vent-radiators-frequently',
        ],
        'improve-heating-installations' => [
            'lower-comfort-heat', 'insulate-hr-boiler-pipes', 'hydronic-balancing',
            'replace-gas-with-infrared-panels',
        ],
        'save-energy-with-warm-tap-water' => [
            'water-saving-shower-head', 'shower-shorter', 'turn-off-boiler',
        ],
        'general' => [
            'only-heat-used-rooms', 'use-insulating-curtains', 'keep-unheated-rooms-closed',
            'use-door-closers',
        ],
    ];

    public function saveValues(): ToolHelper
    {
        // Format isn't applicable for this helper, but it is required due to abstraction
        return $this;
    }

    public function createValues(): ToolHelper
    {
        $this->setValues([
            'updated_measure_ids' => [],
        ]);

        return $this;
    }

    public function createAdvices(): ToolHelper
    {
        $updatedMeasureIds = $this->getValues('updated_measure_ids');

        // NOTE: This will ALWAYS return the quick scan step as that's the first step available in the database.
        // This is also the step the measures are saved on.
        $step = Step::findByShort('small-measures');
        $oldAdvices = UserActionPlanAdviceService::clearForStep($this->user, $this->inputSource, $step);

        // How does one consider a small measure measure? Simple, by answering a related answer as "already-do" or
        // "want-to". Because consistency!
        foreach (static::MEASURE_QUESTION_LINK as $measureShort => $questions) {
            // This returns an array with the answers for these questions. Since they're all string shorts,
            // we can simply work with in_array, without needing to care about whether or not the array is filled,
            // how many answers are set, etc.
            $answersForMeasure = $this->getManyAnswers($questions, true);

            if (in_array('already-do', $answersForMeasure) || in_array('want-to', $answersForMeasure)) {
                // That's it! We consider it now :)

                $measureApplication = MeasureApplication::findByShort($measureShort);
                if ($measureApplication instanceof MeasureApplication) {
                    $actionPlanAdvice = new UserActionPlanAdvice();
                    $actionPlanAdvice->costs = $measureApplication->cost_range ?? ['from' => null, 'to' => null];
                    $actionPlanAdvice->savings_money = $measureApplication->savings_money;
                    $actionPlanAdvice->input_source_id = $this->inputSource->id;
                    $actionPlanAdvice->user()->associate($this->user);
                    $actionPlanAdvice->userActionPlanAdvisable()->associate($measureApplication);
                    $actionPlanAdvice->step()->associate($step);

                    // We only want to check old advices if the updated attributes are not relevant to this measure
                    if (! in_array($measureApplication->id, $updatedMeasureIds) && $this->shouldCheckOldAdvices()) {
                        UserActionPlanAdviceService::checkOldAdvices($actionPlanAdvice, $measureApplication,
                            $oldAdvices);
                    }

                    $actionPlanAdvice->save();
                }
            }
        }

        return $this;
    }
}
