<?php

namespace App\Services\SmartTwin\Mapping\Mappers;

use App\Models\Building;
use App\Services\SmartTwin\Mapping\Elements\PartList;
use App\Services\SmartTwin\Mapping\FieldMapper;
use App\Services\SmartTwin\Mapping\Leaf;
use App\Services\SmartTwin\Mapping\MappingResult;

/**
 * What the installation mappers share (decision 2026-11-07-smarttwin-installaties-mapping).
 *
 * An installation is a list of systems straight under the properties (`heatingSystems`,
 * `pvSystems`, …). As for the parts of the dwelling, every answer is decided at one leaf, the anchor
 * (ElementAnchor): the first field of the mapper's systems in the current situation, or the empty
 * list when there are none, so an answer the response no longer supports is emptied. A mapper that
 * fills the new-situation questions as well gets a second anchor in the scenario. Every other field
 * is skipped, with what it counts towards or why it is not used.
 */
abstract class InstallationMapper implements FieldMapper
{
    protected const CURRENT = 'current';
    protected const SCENARIO = 'scenario';

    public function __construct(protected readonly ElementValues $elementValues)
    {
    }

    /**
     * The systems this mapper reads and their fields, each with its note for the report:
     * `heatingSystems` => [field => note], and a nested list as `heatingSystems.*.generators`.
     *
     * @return array<string, array<string, string>>
     */
    abstract protected function fields(): array;

    /**
     * The answers about the current situation.
     *
     * @param  array<string, mixed>  $response
     * @return array<int, MappingResult>
     */
    abstract protected function current(array $response): array;

    /** Whether this mapper fills new-situation questions from the scenario (see scenario()). */
    protected function fillsScenario(): bool
    {
        return false;
    }

    /**
     * The answers about the new situation, for a mapper that fills those questions.
     *
     * @param  array<string, mixed>  $response
     * @return array<int, MappingResult>
     */
    protected function scenario(array $response): array
    {
        return [];
    }

    /** Why the scenario's fields are skipped, for a mapper without new-situation questions. */
    protected function scenarioNote(): string
    {
        return 'niet gebruikt: het scenario beschrijft de woning na het advies';
    }

    public function paths(): array
    {
        return array_merge($this->pathsIn(self::CURRENT), $this->pathsIn(self::SCENARIO));
    }

    public function map(Building $building, Leaf $leaf, array $response): MappingResult|array
    {
        foreach ([self::CURRENT, self::SCENARIO] as $section) {
            if (ElementAnchor::is($leaf, $response, "{$section}.properties", $this->pathsIn($section))) {
                if (self::CURRENT === $section) {
                    return $this->current($response);
                }

                if ($this->fillsScenario()) {
                    return $this->scenario($response);
                }
            }
        }

        if (str_starts_with($leaf->path, self::SCENARIO . '.') && ! $this->fillsScenario()) {
            return MappingResult::skipped($this->scenarioNote());
        }

        foreach ($this->fields() as $list => $fields) {
            foreach ($fields as $field => $note) {
                if (str_ends_with($leaf->pathGroup, ".{$list}.*.{$field}")) {
                    return MappingResult::skipped($note);
                }
            }
        }

        return MappingResult::skipped('beslist bij het eerste veld van de installatie');
    }

    /** @return array<int, string> */
    private function pathsIn(string $section): array
    {
        $paths = [];

        foreach ($this->fields() as $list => $fields) {
            // The empty list, for a dwelling or a system without any.
            $paths[] = "{$section}.properties.{$list}";

            foreach (array_keys($fields) as $field) {
                $paths[] = "{$section}.properties.{$list}.*.{$field}";
            }
        }

        return $paths;
    }

    /**
     * @param  array<string, mixed>  $response
     */
    protected function systems(array $response, string $section, string $key): PartList
    {
        return PartList::systems($response, $section, $key);
    }

    /**
     * A multiple choice answer, emptied when nothing applies, with the answer's "Anders..." text
     * (`<target>-other`) for what has no option of its own.
     *
     * @param  array<int, string>  $answers
     * @param  array<int, string>  $other   The descriptions of what became "Anders...".
     * @return array<int, MappingResult>
     */
    protected function multipleChoice(string $target, array $answers, array $other, string $note, bool $withOther = true): array
    {
        $answers = array_values(array_unique($answers));
        $results = [
            empty($answers)
                ? MappingResult::cleared($target, $note)
                : MappingResult::mapped($target, $answers, $note),
        ];

        if ($withOther) {
            $other = array_values(array_unique(array_filter($other)));
            $results[] = empty($other)
                ? MappingResult::cleared("{$target}-other", 'geen waarde zonder eigen optie')
                : MappingResult::mapped("{$target}-other", implode(', ', $other), 'de omschrijving van SmartTwin bij "Anders..."');
        }

        return $results;
    }
}
