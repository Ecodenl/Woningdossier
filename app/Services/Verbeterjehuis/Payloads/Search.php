<?php

namespace App\Services\Verbeterjehuis\Payloads;

use App\Models\Building;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\ToolQuestion;
use App\Services\MappingService;
use App\Services\Verbeterjehuis\RegulationService;
use App\Traits\FluentCaller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

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
        $targets = MappingService::init()->from($measureModel)->resolveTarget();
        if (! $measureModel instanceof MeasureApplication) {
             // when its not a measure application, it will be a cooperation measure or custom measure
             // those are mapped to a measure category
             // that means the target is a mapping category
             // so retrieve the vbjehuis measures from the measure category
            $targets = MappingService::init()->from($targets->first())->resolveTarget();
        }

        $values = [];
        if ($targets->isNotEmpty()) {
            foreach ($targets as $target) {
                if (is_array($target)) {
                    $values[] = $target['Value'];
                }
            }
        }

        if (! empty($values)) {
            $this->transformedPayload = $this->transformedPayload->filter(function ($regulation) use ($values) {
                $relevantTags = array_filter($regulation['Tags'], fn($tag) => in_array($tag['Value'], $values));
                return ! empty($relevantTags);
            });
        } else {
            // so there is no mapping available
            // which in a sense means that there are no relevant regulations.
            // this is why we clear it.
            // ALSO worth noting, this is only possible because not every measure has a mapping
            // for instance the forBuildingContractType will always have a mapping available.
            $this->transformedPayload = collect();
        }

        return $this;
    }

    public function forBuildingContractType(Building $building, InputSource $inputSource, ?string $answer = null): self
    {
        // this question will give us the answer about which type of building the user has
        // rent / homeowner
        $toolQuestion = ToolQuestion::findByShort('building-contract-type');
        $toolQuestionCustomValue = $toolQuestion
            ->toolQuestionCustomValues()
            ->visible()
            ->where(
                'short',
                $answer ?? $building->getAnswer($inputSource, $toolQuestion)
            )
            ->first();

        // If we get more measures for one value, we should change this.
        $target = MappingService::init()->from($toolQuestionCustomValue)->resolveTarget()->first();
//        Log::debug('contractType', $toolQuestionCustomValue->toArray());
//        Log::debug('contractType', $target);
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
