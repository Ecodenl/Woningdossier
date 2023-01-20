<?php

namespace App\Services\Verbeterjehuis\Payloads;

use App\Models\MeasureApplication;
use App\Services\MappingService;
use App\Traits\FluentCaller;
use Illuminate\Support\Collection;

class Search implements VerbeterjehuisPayload
{
    use FluentCaller;

    public Collection $payload;

    public function __construct(array $payload)
    {
        $this->payload = collect($payload);
    }

    public function forMeasureApplication(MeasureApplication $measureApplication): array
    {
        $target = MappingService::init()->from($measureApplication)->resolveTarget();

        $relevantRegulations = [];

        $this->payload->map(function ($regulation) use ($target, &$relevantRegulations) {
            $relevantTags = array_filter($regulation['Tags'], fn($tag) => $tag['Value'] === $target['Value']);
            if ( ! empty($relevantTags)) {
                $relevantRegulations[] = $regulation;
            }
        });

        return $relevantRegulations;
    }
}