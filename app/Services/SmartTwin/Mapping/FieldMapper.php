<?php

namespace App\Services\SmartTwin\Mapping;

use App\Models\Building;

/**
 * Maps one kind of field of a SmartTwin response onto our own data.
 *
 * Implementations are registered in MapperRegistry. Adding a mapping is one class plus one line
 * there; nothing else in the pipeline changes, and the fields it claims leave the report's
 * `unmapped` rows by themselves.
 */
interface FieldMapper
{
    /**
     * The path groups this mapper claims, e.g.
     * `current.properties.facadeAssemblies.*.facades.*.rcValue`.
     *
     * Path groups rather than paths: a response with twelve facades produces twelve leaves that
     * differ only in their index, and they are one mapping decision, not twelve.
     *
     * @return array<int, string>
     */
    public function paths(): array;

    /**
     * @param  array<string, mixed>  $response  The whole response, so a mapper that needs to combine
     *                                          sibling fields can reach them. Mark the siblings it
     *                                          consumes as skipped with a note pointing here, so the
     *                                          report stays honest about them.
     */
    public function map(Building $building, Leaf $leaf, array $response): MappingResult;
}
