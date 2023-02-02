<?php

namespace App\Services;

use App\Models\Mapping;
use App\Models\User;
use App\Traits\FluentCaller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MappingService
{
    use FluentCaller;

    public $from;
    public $target;

    public function __construct(){}

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
            if (! empty($mapping->target_data)) {
                return $mapping->target_data;
            }
            if (! is_null($mapping->target_value)) {
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

    public function sync($syncableData): void
    {
        // first we will remove the current rows.
        Mapping::where($this->whereFrom())->delete();

        $attributes = [];
        foreach ($syncableData as $index => $target) {
            $attributes[$index] = $this->whereFrom();
            // In the case we EVER allow different types for mapping, we must ensure other fields get nullified.
            if ($this->target instanceof Model) {
                $attributes[$index]['target_model_type'] = $target->getMorphClass();
                $attributes[$index]['target_model_id'] = $target->id;
            } else {
                if (is_array($target)) {
                    $attributes[$index]['target_data'] = json_encode($target);
                } else {
                    $attributes[$index]['target_value'] = $target;
                }
            }
        }

        DB::table((new Mapping())->getTable())
            ->insert($attributes);
    }
}