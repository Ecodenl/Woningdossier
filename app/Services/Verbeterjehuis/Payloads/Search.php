<?php

namespace App\Services\Verbeterjehuis\Payloads;

use App\Models\Building;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\ToolQuestion;
use App\Services\MappingService;
use App\Traits\FluentCaller;
use Illuminate\Support\Collection;

class Search implements VerbeterjehuisPayload
{
    use FluentCaller;

    public Collection $payload;
    public Collection $transformedPayload;

    public function __construct(array $payload)
    {
        $this->payload = collect($payload);
        $this->transformedPayload = $this->prepare();
    }

    public function prepare()
    {
        // nope, do this on cache
        dd($this->payload);
    }

    public function forMeasureApplication(MeasureApplication $measureApplication): self
    {
        $target = MappingService::init()->from($measureApplication)->resolveTarget();

        $this->transformedPayload = $this->transformedPayload->filter(function ($regulation) use ($target) {
            $relevantTags = array_filter($regulation['Tags'], fn($tag) => $tag['Value'] === $target['Value']);

            return  ! empty($relevantTags);
        });

        return $this;
    }

    public function forBuildingContractType(Building $building): self
    {
        // this question will give us the answer about which type of building the user has
        // rent / homeowner
        $toolQuestion = ToolQuestion::findByShort('building-contract-type');
        $toolQuestionCustomValue = $toolQuestion->toolQuestionCustomValues()->visible()->where('short', '=',
            $building->getAnswer(InputSource::findByShort('master'), $toolQuestion))
            ->first();
        $target = MappingService::init()->from($toolQuestionCustomValue)->resolveTarget();

        $this->transformedPayload = $this->transformedPayload;
        dd($target);
    }
}