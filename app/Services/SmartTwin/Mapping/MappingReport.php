<?php

namespace App\Services\SmartTwin\Mapping;

use App\Enums\SmartTwin\EventType;
use App\Enums\SmartTwin\MappingStatus;
use App\Models\Building;
use Throwable;

/**
 * The outcome of mapping one response: one entry per leaf, always.
 *
 * Complete by construction, because the walk that fills it runs over the response rather than over
 * the mappings we happen to know. A field we have never seen before still gets an entry.
 */
final class MappingReport
{
    /**
     * @var array<int, MappingEntry>
     */
    private array $entries = [];

    public function __construct(
        public readonly Building $building,
        public readonly EventType $eventType,
    )
    {
    }

    public function add(Leaf $leaf, MappingResult $result): void
    {
        $this->entries[] = MappingEntry::fromResult($leaf, $result);
    }

    public function unmapped(Leaf $leaf): void
    {
        $this->entries[] = new MappingEntry($leaf, MappingStatus::UNMAPPED);
    }

    public function error(Leaf $leaf, Throwable $exception): void
    {
        $this->entries[] = new MappingEntry(
            $leaf,
            MappingStatus::ERROR,
            null,
            $exception->getMessage(),
        );
    }

    /**
     * @return array<int, MappingEntry>
     */
    public function entries(): array
    {
        return $this->entries;
    }

    /**
     * @return array<int, MappingEntry>
     */
    public function entriesNeedingAttention(): array
    {
        return array_values(array_filter(
            $this->entries,
            fn (MappingEntry $entry) => $entry->status->needsAttention(),
        ));
    }

    /**
     * Counts per status, every status present so a zero stays visible in the log line.
     *
     * @return array<string, int>
     */
    public function counts(): array
    {
        $counts = [];

        foreach (MappingStatus::cases() as $status) {
            $counts[$status->value] = 0;
        }

        foreach ($this->entries as $entry) {
            $counts[$entry->status->value]++;
        }

        return $counts;
    }
}
