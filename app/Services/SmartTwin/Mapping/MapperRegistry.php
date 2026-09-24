<?php

namespace App\Services\SmartTwin\Mapping;

/**
 * Path group -> mapper. This is the list that grows while the mapping is being built; everything
 * around it stays as it is.
 *
 * What a mapper does not claim is reported as unmapped, which is not a broken run but the field
 * inventory of a real response — and the list of what is still to do.
 */
class MapperRegistry
{
    /**
     * The one list to add to when a mapping is built. Nothing else in the pipeline changes with it.
     *
     * @var array<int, class-string<FieldMapper>>
     */
    private const MAPPERS = [
        Mappers\WallInsulationMapper::class,
        Mappers\FloorInsulationMapper::class,
        Mappers\SolutionsMapper::class,
    ];

    /**
     * @var null|array<string, FieldMapper>
     */
    private ?array $byPathGroup = null;

    /**
     * @param  array<int, class-string<FieldMapper>>  $mappers
     */
    public function __construct(private readonly array $mappers = self::MAPPERS)
    {
    }

    public function forPath(string $pathGroup): ?FieldMapper
    {
        return $this->index()[$pathGroup] ?? null;
    }

    /**
     * @return array<string, FieldMapper>
     */
    private function index(): array
    {
        if (! is_null($this->byPathGroup)) {
            return $this->byPathGroup;
        }

        $this->byPathGroup = [];

        foreach ($this->mappers as $mapper) {
            /** @var FieldMapper $mapper */
            $mapper = app($mapper);

            foreach ($mapper->paths() as $pathGroup) {
                $this->byPathGroup[$pathGroup] = $mapper;
            }
        }

        return $this->byPathGroup;
    }
}
