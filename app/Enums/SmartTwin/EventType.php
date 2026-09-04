<?php

namespace App\Enums\SmartTwin;

use App\Models\InputSource;

enum EventType: string
{
    case RESIDENT_SCAN_FINISHED = 'smarttwin.quickscan.finalized';
    case COACH_SCAN_FINISHED    = 'smarttwin.advice.finalized';

    /**
     * The input source the results of this flow are stored under.
     */
    public function inputSource(): InputSource
    {
        return match ($this) {
            self::RESIDENT_SCAN_FINISHED => InputSource::resident(),
            self::COACH_SCAN_FINISHED    => InputSource::coach(),
        };
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
