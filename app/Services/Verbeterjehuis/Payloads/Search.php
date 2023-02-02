<?php

namespace App\Services\Verbeterjehuis\Payloads;

use App\Models\Building;
use App\Models\InputSource;
use App\Models\ToolQuestion;
use App\Services\MappingService;
use App\Services\Verbeterjehuis\RegulationService;
use App\Traits\FluentCaller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Search
{
    use FluentCaller;

    public Collection $payload;
    public Collection $transformedPayload;

    public function __construct(array $payload)
    {
        $this->payload = collect($payload);
        $this->transformedPayload = $this->prepare();
    }

    public function prepare(): Collection
    {
        return $this->payload->map(function (array $regulation) {
            $regulation['TargetGroup'] = explode(', ', $regulation['TargetGroup']);
            return $regulation;
        });
    }

    public function forMeasure(Model $measureModel): self
    {
        $target = MappingService::init()->from($measureModel)->resolveTarget();

        if (is_array($target)) {
            $this->transformedPayload = $this->transformedPayload->filter(function ($regulation) use ($target) {
                $relevantTags = array_filter($regulation['Tags'], fn($tag) => $tag['Value'] === $target['Value']);
                return ! empty($relevantTags);
            });
        }
        if (is_null($target)) {
            // so there is no mapping available
            // which in a sense means that there are no relevant regulations.
            // this is why we clear it.
            // ALSO worth noting, this is only possible because not every measure has a mapping
            // for instance the forBuildingContractType will always have a mapping available.
            $this->transformedPayload = collect();
        }

        return $this;
    }

    public function forBuildingContractType(Building $building, InputSource $inputSource): self
    {
        // this question will give us the answer about which type of building the user has
        // rent / homeowner
        $toolQuestion = ToolQuestion::findByShort('building-contract-type');
        $toolQuestionCustomValue = $toolQuestion
            ->toolQuestionCustomValues()
            ->visible()
            ->where(
                'short',
                $building->getAnswer($inputSource, $toolQuestion)
            )
            ->first();

        $target = MappingService::init()->from($toolQuestionCustomValue)->resolveTarget();
        if (is_array($target)) {
            $this->transformedPayload = $this->transformedPayload->filter(function ($regulation) use ($target) {
                return in_array($target['Value'], $regulation['TargetGroup']);
            });
        }
        return $this;
    }

    public function getLoans(): Collection
    {
        return $this->transformedPayload->where('Type', RegulationService::LOAN);
    }

    public function getSubsidies(): Collection
    {
        return $this->transformedPayload->where('Type', RegulationService::SUBSIDY);
    }

    public function getCategorized(): Collection
    {
        return $this->transformedPayload->groupBy('Type');
    }

    public function all(): Collection
    {
        return $this->transformedPayload;
    }
}