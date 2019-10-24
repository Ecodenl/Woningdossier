<?php

namespace App\Observers;

use App\Models\Cooperation;

class CooperationObserver
{
    /**
     * For every cooperation that is created, we attach all the steps to it.
     *
     * @param Cooperation $cooperation
     */
    public function created(Cooperation $cooperation)
    {
        $steps = \DB::table('steps')->get();

        foreach ($steps as $step) {
            \DB::table('cooperation_steps')->insert(['cooperation_id' => $cooperation->id, 'step_id' => $step->id, 'order' => $step->order]);
        }
    }
}
