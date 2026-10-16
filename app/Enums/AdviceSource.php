<?php

namespace App\Enums;

/**
 * Who owns a row in `user_action_plan_advices`.
 *
 * Null — the overwhelming majority — means the calculation owns it: it is thrown away and rebuilt
 * whenever the step is recalculated, which is what keeps an advice in step with the dossier.
 *
 * A value here means someone else produced it and the calculation must leave it alone, both when
 * clearing a step and when building a new advice for the same measure. Without that, a recalculation
 * either deletes the advice or puts a second card for the same measure next to it.
 */
enum AdviceSource: string
{
    /**
     * Mapped from a SmartTwin advice. SmartTwin prices the measures it advises, and those prices are
     * the ones the resident should see — not a figure Hoomdossier derived separately.
     */
    case SMART_TWIN = 'smarttwin';
}
