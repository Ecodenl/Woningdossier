<?php

namespace App\Services\Verbeterjehuis;

use App\Models\Building;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\ToolQuestion;
use App\Models\UserActionPlanAdvice;
use App\Services\MappingService;
use App\Services\Verbeterjehuis\Payloads\Search;
use App\Services\Verbeterjehuis\Payloads\VerbeterjehuisPayload;
use App\Traits\FluentCaller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class RegulationService
{
    use FluentCaller;

    public Building $building;

    public array $context = [];

    public function fromBuilding(Building $building): self
    {
        $this->building = $building;
        return $this;
    }

    private function getCacheKey(): string
    {
        return md5(implode('|', $this->context));
    }

    public function fetch(): array
    {
        $building = $this->building;
        // ofcourse this should be resolved through the mapping service, but thats for when the bag update is done
        $this->context['cityId'] = 3336;

        return Cache::driver('database')->remember($this->getCacheKey(), Carbon::now()->addDay(), function () {
            return Verbeterjehuis::init(Client::init())
                ->regulation()
                ->search($this->context);
        });
    }

    public function get(): VerbeterjehuisPayload
    {
        return Search::init($this->fetch());
    }
}