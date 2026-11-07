<?php

namespace App\Enums\SmartTwin;

use App\Models\InputSource;

enum EventType: string
{
    case RESIDENT_SCAN_FINISHED = 'smarttwin.quickscan.finalized';
    case COACH_SCAN_FINISHED    = 'smarttwin.advice.finalized';

    /**
     * A coach started an advice in the advisor tool. No results yet: it locks the resident's
     * woonplan until the coach is done (see WoonplanLock).
     */
    case COACH_ADVICE_STARTED   = 'smarttwin.advice.started';

    /**
     * The input source the results of this flow are stored under, and whose user the callback is
     * about. A started advice is the coach's flow, like the finished one.
     */
    public function inputSource(): InputSource
    {
        return match ($this) {
            self::RESIDENT_SCAN_FINISHED                          => InputSource::resident(),
            self::COACH_SCAN_FINISHED, self::COACH_ADVICE_STARTED => InputSource::coach(),
        };
    }

    /**
     * Whether SmartTwin has results to fetch for this event. Only those are stored as a callback:
     * a stored callback is what makes the results get fetched.
     */
    public function bringsResults(): bool
    {
        return self::COACH_ADVICE_STARTED !== $this;
    }

    /**
     * The reverse of inputSource(), so a stored artifact can be traced back to the flow that
     * produced it. Null for the input sources SmartTwin never writes to.
     */
    public static function tryFromInputSource(InputSource $inputSource): ?self
    {
        return match ($inputSource->short) {
            InputSource::RESIDENT_SHORT => self::RESIDENT_SCAN_FINISHED,
            InputSource::COACH_SHORT    => self::COACH_SCAN_FINISHED,
            default                     => null,
        };
    }
}
