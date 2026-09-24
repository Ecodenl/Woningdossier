<?php

namespace App\Enums\SmartTwin;

/**
 * What the mapping did with one field of a SmartTwin response.
 *
 * The descriptions travel along into the mapping report as a column of their own, so the CSV
 * explains its own statuses to whoever opens it without the documentation at hand.
 */
enum MappingStatus: string
{
    case MAPPED = 'mapped';
    case UNMAPPED = 'unmapped';
    case VALUE_UNMAPPED = 'value-unmapped';
    case TARGET_MISSING = 'target-missing';
    case SKIPPED = 'skipped';
    case ERROR = 'error';

    public function description(): string
    {
        return match ($this) {
            self::MAPPED         => 'Gemapt en opgeslagen',
            self::UNMAPPED       => 'Geen mapping gedefinieerd voor dit veld',
            self::VALUE_UNMAPPED => 'Veld is gemapt, maar deze waarde kent geen tegenhanger',
            self::TARGET_MISSING => 'Mapping verwijst naar een bestemming die niet bestaat',
            self::SKIPPED        => 'Bewust genegeerd',
            self::ERROR          => 'Fout tijdens het mappen',
        };
    }

    /**
     * Statuses that mean the mapping itself is broken, rather than merely incomplete. Those belong
     * in the log — and therefore in Sentry — instead of only in a CSV nobody opens until they go
     * looking.
     */
    public function needsAttention(): bool
    {
        return in_array($this, [self::ERROR, self::TARGET_MISSING], true);
    }
}
