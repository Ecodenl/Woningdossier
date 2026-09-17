<?php

namespace App\Services\SmartTwin\Mapping\Mappers;

use App\Models\Building;
use App\Services\SmartTwin\Mapping\FieldMapper;
use App\Services\SmartTwin\Mapping\Leaf;
use App\Services\SmartTwin\Mapping\MappingResult;

/**
 * The measures SmartTwin advises, and what they cost.
 *
 * Unlike the facade questions, where many leaves collapse into one answer, each solution is its own
 * entry in the action plan. The id leaf carries it; everything hanging off that solution is skipped
 * with a note saying where it ended up.
 *
 * No saving is written. SmartTwin gives one figure for the whole package and none per measure, so
 * there is nothing to put on a card — see the note on energyCostSavingsPerMonth below.
 */
class SolutionsMapper implements FieldMapper
{
    private const SOLUTION = 'scenario.solutions.*';
    private const COST = self::SOLUTION . '.adviceSolutionCost';

    private const PATH_ID = self::SOLUTION . '.id';
    private const PATH_NAME = self::SOLUTION . '.name';
    private const PATH_DETAILS_EMPTY = self::SOLUTION . '.details';
    private const PATH_TOTAL_COST = self::COST . '.totalCostWithTax';
    private const PATH_ADDITIONAL_EMPTY = self::COST . '.additionalCosts';
    private const PATH_EXCEPTIONAL_EMPTY = self::COST . '.exceptionalCosts';
    private const PATH_SCENARIO_TOTAL = 'scenario.totalCostWithTax';
    private const PATH_SCENARIO_SAVINGS = 'scenario.energyCostSavingsPerMonth';
    private const PATH_TOP_LEVEL_SOLUTIONS = 'solutions';

    public function __construct(private readonly SolutionMeasures $measures)
    {
    }

    public function paths(): array
    {
        $fields = ['code', 'costWithTax', 'description', 'quantity', 'title', 'unit'];

        return array_merge(
            [
                self::PATH_ID,
                self::PATH_NAME,
                self::PATH_DETAILS_EMPTY,
                self::PATH_TOTAL_COST,
                self::PATH_ADDITIONAL_EMPTY,
                self::PATH_EXCEPTIONAL_EMPTY,
                self::PATH_SCENARIO_TOTAL,
                self::PATH_SCENARIO_SAVINGS,
                self::PATH_TOP_LEVEL_SOLUTIONS,
            ],
            array_map(fn (string $field) => self::SOLUTION . ".details.*.{$field}", ['displayName', 'id', 'unit', 'value']),
            array_map(fn (string $field) => self::COST . ".primaryCosts.*.{$field}", $fields),
            array_map(fn (string $field) => self::COST . ".additionalCosts.*.{$field}", $fields),
            array_map(fn (string $field) => self::COST . ".exceptionalCosts.*.{$field}", $fields),
        );
    }

    public function map(Building $building, Leaf $leaf, array $response): MappingResult
    {
        return match ($leaf->pathGroup) {
            self::PATH_ID => $this->advice($leaf, $response),

            // SmartTwin's own label for the product. The card shows the measure's name as
            // Hoomdossier knows it, so the two stay consistent with the rest of the action plan.
            self::PATH_NAME => MappingResult::skipped('maatregelnaam komt uit Hoomdossier zelf'),

            // Includes the optional additional and exceptional costs; the advice is written on the
            // sum of primaryCosts alone. On the pitched roof of dossier 100261 that is the
            // difference between € 4.254,96 and € 4.777,22.
            self::PATH_TOTAL_COST => MappingResult::skipped('bevat bijkomende kosten, de maatregel rekent met primaryCosts'),

            self::PATH_SCENARIO_TOTAL => MappingResult::skipped('som van de maatregelen die elk hun eigen kosten krijgen'),

            // The gap behind A6: one figure for the whole package, nothing per measure, while the
            // woonplan shows a saving per card. Recorded here so the report says why every card
            // SmartTwin produced comes without one.
            self::PATH_SCENARIO_SAVINGS => MappingResult::skipped('besparing geldt het hele pakket, niet een losse maatregel'),

            self::PATH_TOP_LEVEL_SOLUTIONS => MappingResult::skipped('leeg; de maatregelen staan onder scenario'),

            default => $this->partOfASolution($leaf),
        };
    }

    /**
     * Everything hanging off a solution that the advice does not use directly.
     *
     * @param  Leaf  $leaf
     */
    private function partOfASolution(Leaf $leaf): MappingResult
    {
        return match (true) {
            str_contains($leaf->pathGroup, '.primaryCosts')    => MappingResult::skipped('telt mee in de kosten van de maatregel'),
            str_contains($leaf->pathGroup, '.additionalCosts')  => MappingResult::skipped('optionele post, hoort niet in het woonplan'),
            str_contains($leaf->pathGroup, '.exceptionalCosts') => MappingResult::skipped('optionele post, hoort niet in het woonplan'),
            str_contains($leaf->pathGroup, '.details')          => MappingResult::skipped('producteigenschap, geen dossiergegeven'),
            default                                             => MappingResult::skipped('path group wordt geclaimd maar niet afgehandeld'),
        };
    }

    /**
     * @param  array<string, mixed>  $response
     */
    private function advice(Leaf $leaf, array $response): MappingResult
    {
        $solution = $this->solutionOf($leaf, $response);

        if (is_null($solution)) {
            return MappingResult::valueUnmapped('solution niet terug te vinden in de response');
        }

        $solutionId = (string) $leaf->value;
        $measureShort = $this->measures->shortFor($solutionId);

        if (is_null($measureShort)) {
            // A solution we have no coupling for. Not an error: SmartTwin adds products, and this is
            // the signal that one of them needs a row in `mappings` before it can reach a woonplan.
            return MappingResult::valueUnmapped("geen maatregel gekoppeld aan solution id '{$solutionId}'");
        }

        $costs = $this->primaryCostTotal($solution);

        if ($costs <= 0) {
            return MappingResult::skipped("'{$measureShort}' heeft geen basiskosten");
        }

        return MappingResult::mappedAdvice(
            $measureShort,
            $costs,
            "som van de basiskosten van solution '{$solutionId}'",
        );
    }

    /**
     * What the measure costs: the primary costs, which SmartTwin calls "Basis kosten".
     *
     * Not totalCostWithTax. That folds in the additional and exceptional costs, which their own
     * spec calls optional — a coach may or may not have included them, and the woonplan should not
     * quote a price that depends on which.
     *
     * @param  array<string, mixed>  $solution
     */
    private function primaryCostTotal(array $solution): float
    {
        return array_sum(array_map(
            fn (array $cost) => (float) ($cost['costWithTax'] ?? 0),
            $solution['adviceSolutionCost']['primaryCosts'] ?? [],
        ));
    }

    /**
     * The solution this leaf belongs to. Taken by position: the leaf's path holds the index it came
     * from, which is exact even when two solutions share an id.
     *
     * @param  array<string, mixed>  $response
     * @return null|array<string, mixed>
     */
    private function solutionOf(Leaf $leaf, array $response): ?array
    {
        $index = explode('.', $leaf->path)[2] ?? null;

        return $response['scenario']['solutions'][$index] ?? null;
    }
}
