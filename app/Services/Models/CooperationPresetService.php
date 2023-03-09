<?php

namespace App\Services\Models;

use App\Helpers\Wrapper;
use App\Models\CooperationPreset;
use App\Models\MeasureCategory;
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
                    $measureCategory = MeasureCategory::find($values['measure_category'] ?? null);
                    if ($measureCategory instanceof MeasureCategory) {
                        MappingService::init()->from($this->model)->sync([$measureCategory]);
                    }
                    break;

                default:
                    // Not relevant rn, but simple structure logic (might need createMany?)
                    $this->model->{$relation}()->create($values);
                    break;
            }
        }
    }
}