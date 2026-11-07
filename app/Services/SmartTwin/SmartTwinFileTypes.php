<?php

namespace App\Services\SmartTwin;

/**
 * The file type shorts SmartTwin writes to FileStorage. They're grouped here because two places
 * have to agree on the list: the super admin debug page that surfaces them, and the cooperation
 * report page that must NOT list them (they're per building, not cooperation wide).
 */
final class SmartTwinFileTypes
{
    /**
     * The raw advice/quickscan response, one JSON per building + flow.
     */
    public const ADVICE_RAW = 'smarttwin-advice-raw';

    /**
     * Phase 2: what the mapping did with every field of one response, one CSV per JSON. No file
     * type row is seeded for it yet; listing the short here already keeps it out of the cooperation
     * reports, and out of their policy, once it is.
     */
    public const MAPPING_REPORT = 'smarttwin-mapping-report';

    /**
     * @return array<int, string>
     */
    public static function all(): array
    {
        return [
            self::ADVICE_RAW,
            self::MAPPING_REPORT,
        ];
    }
}
