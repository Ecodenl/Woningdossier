<?php

namespace App\Services\SmartTwin\Mapping\Elements;

/**
 * One element of the dwelling as one section of a response describes it: a summary line, the alike
 * parts grouped into lines, and the ids of the parts it was read from.
 *
 * The same shape for every element, so whatever shows it — the advice details, the explanation page
 * — handles one form.
 */
final class ElementState
{
    /**
     * @param  null|array<string, mixed>        $summary  Null when the section holds none of it.
     * @param  array<int, array<string, mixed>> $lines
     * @param  array<int, string>               $partIds
     */
    public function __construct(
        public readonly ?array $summary,
        public readonly array $lines,
        public readonly array $partIds,
    )
    {
    }

    /**
     * @return array{summary: null|array<string, mixed>, lines: array<int, array<string, mixed>>}
     */
    public function toArray(): array
    {
        return ['summary' => $this->summary, 'lines' => $this->lines];
    }
}
