<?php

namespace App\Services\Verbeterjehuis;

use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\ToolQuestion;
use App\Models\UserActionPlanAdvice;
use App\Services\MappingService;
use App\Traits\FluentCaller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class RegulationService
{
    use FluentCaller;

    public UserActionPlanAdvice $userActionPlanAdvice;
    public array $context = [];

    public function fromUserActionPlanAdvice(UserActionPlanAdvice $userActionPlanAdvice)
    {
        $this->userActionPlanAdvice = $userActionPlanAdvice;
        return $this;
    }

    private function getCacheKey(): string
    {
        return md5(implode('|', $this->context));
    }

    public function fetch()
    {
        $mappingService = MappingService::init();

        $user = $this->userActionPlanAdvice->user;
        $building = $user->building;
        $userActionPlanAdvisable = $this->userActionPlanAdvice->userActionPlanAdvisable;

        if ($userActionPlanAdvisable instanceof MeasureApplication) {
            $this->context['measures'] = $mappingService->from($userActionPlanAdvisable)->resolveTarget()['Value'];
        }

        // so this kind of sucks..
        // the getAnswer returns the answer its short, NOT the answer model itself (toolQuestionCustomValue)
        // so we will have to query that again to get the model and make sure the resolving goes well.
        $buildingContractType = ToolQuestion::findByShort('building-contract-type');
        $buildingContractTypeShortAnswer = $building->getAnswer(
            InputSource::findByShort('master'),
            $buildingContractType
        );

        $toolQuestionCustomValue = $buildingContractType->toolQuestionCustomValues()->where('short', $buildingContractTypeShortAnswer)->first();
        $this->context['targetGroup'] = $mappingService->from($toolQuestionCustomValue)->resolveTarget()['Value'];

        // ofcourse this should be resolved through the mapping service, but thats for later on.
        $this->context['cityId'] = 3336;

        return Cache::driver('database')->remember($this->getCacheKey(), Carbon::now()->addDay(), function () {
            return $regulations = Verbeterjehuis::init(Client::init())
                ->regulation()
                ->search($this->context);
        });
    }
}