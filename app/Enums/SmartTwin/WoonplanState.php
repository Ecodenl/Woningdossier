<?php

namespace App\Enums\SmartTwin;

/**
 * Where the resident's woonplan stands while a coach works on it in SmartTwin.
 */
enum WoonplanState: string
{
    /** No coach at work, or the coach is done: the resident may change the plan. */
    case OPEN = 'open';

    /** A coach started an advice and has not delivered one yet: "Nog even geduld". */
    case AWAITING_COACH = 'awaiting-coach';

    /** The coach's plan is in, but the coach is not done: shown to the resident, not editable. */
    case LOCKED = 'locked';

    /** Whether the resident may change the plan, or start a new calculation in SmartTwin. */
    public function residentMayEdit(): bool
    {
        return self::OPEN === $this;
    }
}
