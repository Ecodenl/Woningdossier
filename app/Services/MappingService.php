<?php

namespace App\Services;

use App\Models\Mapping;
use App\Traits\FluentCaller;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\Mixed_;

class MappingService
{
    use FluentCaller;

    public $from;
    public $target;

    public function __construct()
    {
    }

    public function from($from): self
    {
        $this->from = $from;
        return $this;
    }

    public function target($target): self
    {
        $this->target = $target;
        return $this;
    }

    public function resolveMapping(): ?Mapping
    {
        return Mapping::where($this->whereFrom())->first();
    }

    public function resolveTarget()
    {
        $mapping = $this->resolveMapping();
        if ($mapping instanceof Mapping) {

            if ( ! empty($mapping->target_data)) {
                return $mapping->target_data;
            }
            if ( ! is_null($mapping->target_value)) {
                return $mapping->target_value;
            }
        }
        // todo: implement return morph model
        return null;
    }

    public function whereFrom(): array
    {
        if ($this->from instanceof Model) {
            return [
                'from_model_type' => $this->from->getMorphClass(),
                'from_model_id' => $this->from->id,
            ];
        }
        return ['from_value' => $this->from];
    }

    public function sync(): Mapping
    {
        $mapping = [];

        if ($this->target instanceof Model) {
            $mapping['target_model_type'] = $this->target->getMorphClass();
            $mapping['target_model_id'] = $this->target->id;
        } else {
            if (is_array($this->target)) {
                $mapping['target_data'] = $this->target;
            } else {
                $mapping['target_value'] = $this->target;
            }
        }

        return Mapping::updateOrCreate($this->whereFrom(), $mapping);
    }
}