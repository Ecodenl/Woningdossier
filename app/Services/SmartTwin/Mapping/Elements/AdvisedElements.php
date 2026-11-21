<?php

namespace App\Services\SmartTwin\Mapping\Elements;

use App\Services\SmartTwin\Mapping\Mappers\SolutionMeasures;

/**
 * Which parts of the dwelling SmartTwin's solutions work on.
 *
 * The answers do not depend on it — they come from the properties alone. It is what lets a mapper
 * notice that the two halves of a response disagree: a floor solution next to a floor the scenario
 * leaves untouched, or a facade the scenario insulates without a solution for it. SmartTwin's own
 * spec rules that out ("the resulting building model after applying the solutions"), so such a
 * response is reported rather than repaired.
 *
 * A solution without a coupled measure covers nothing: there is no step to read the element from.
 */
class AdvisedElements
{
    public function __construct(private readonly SolutionMeasures $measures)
    {
    }

    /**
     * @param  array<string, mixed>  $response
     */
    public function covers(array $response, Element $element, ?RoofShape $shape = null): bool
    {
        foreach ($this->scopes($response) as $scope) {
            if ($scope->covers($element, $shape)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<string, mixed>  $response
     * @return array<int, ElementScope>
     */
    public function scopes(array $response): array
    {
        $scopes = [];

        foreach ($response['scenario']['solutions'] ?? [] as $solution) {
            $measureShort = $this->measures->shortFor((string) ($solution['id'] ?? ''));

            if (! is_null($measureShort)) {
                array_push($scopes, ...ElementScope::forMeasure($measureShort, $this->measures->stepFor($measureShort)));
            }
        }

        return $scopes;
    }
}
