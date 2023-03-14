<?php

namespace App\Helpers\Conditions\Evaluators;

use App\Helpers\Wrapper;
use App\Services\Verbeterjehuis\Payloads\Search;
use App\Services\Verbeterjehuis\RegulationService;

class MeasureHasSubsidy extends ShouldEvaluate
{
    public function evaluate($value = null): array
    {
        $building = $this->building;
        $inputSource = $this->inputSource;
        $answers = $this->answers;

        // This evaluator checks if the given measure has subsidies available.
        // $value must be an array, where
        // 'advisable_type' => The morph class
        // 'advisable_id' => The morph ID

        $advisable = $value['advisable_type']::find($value['advisable_id']);

        $key = md5(json_encode($value));

        if (array_key_exists($key, $this->override)) {
            $bool = $this->override[$key];
            return [
                'results' => $bool,
                'bool' => $bool,
                'key' => $key,
            ];
        }

        $payload = Wrapper::wrapCall(fn () => RegulationService::init()
            ->forBuilding($building)
            ->getSearch());

        if ($payload instanceof Search) {
            $regulations = $payload
                ->forMeasure($advisable)
                ->forBuildingContractType($building, $inputSource, $this->getAnswer('building-contract-type'));

            $bool = $regulations->getSubsidies()->isNotEmpty();

            return [
                'results' => $bool,
                'bool' => $bool,
                'key' => $key,
            ];
        }

        return [
            'results' => false,
            'bool' => false,
            'key' => $key,
        ];
    }
}