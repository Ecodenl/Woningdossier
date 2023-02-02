<?php

namespace App\Services\Models;

use App\Models\CooperationPreset;
use App\Services\MappingService;
use App\Services\Verbeterjehuis\RegulationService;
use App\Traits\FluentCaller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class CooperationPresetService
{
    use FluentCaller;

    const COOPERATION_MEASURE_APPLICATIONS = 'cooperation-measure-applications';

    protected CooperationPreset $cooperationPreset;
    protected Model $model;

    public function forPreset(CooperationPreset $cooperationPreset): self
    {
        $this->cooperationPreset = $cooperationPreset;
        return $this;
    }

    public function forModel(Model $model): self
    {
        $this->model = $model;
        return $this;
    }

    public function getContent(): array
    {
        return $this->cooperationPreset->cooperationPresetContents()->pluck('content')->toArray();
    }

    public function handleRelations(array $relations)
    {
        foreach ($relations as $relation => $values) {
            switch ($relation) {
                case 'mapping':
                    $measureCategory = $values['measure_category'];
                    $targetData = Arr::first(Arr::where(RegulationService::init()->getFilters()['Measures'], fn ($a) => $a['Value'] === $measureCategory));
                    MappingService::init()->from($this->model)->sync([$targetData]);
                    break;

                default:
                    // Not relevant rn, but simple structure logic (might need createMany?)
                    $this->model->{$relation}()->create($values);
                    break;
            }
        }
    }
}