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

    public function exists(): bool
    {
        return $this->resolveMapping() instanceof Mapping;
    }

    public function doesntExist(): bool
    {
        return ! $this->exists();
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
            if ( ! is_null($mapping->target_model_type)) {
                return $mapping->mapable;
            }
        }
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

    public function sync(array $syncableData = [], ?string $type = null): void
    {
        // first we will remove the current rows.
        Mapping::where($this->whereFrom())->delete();

        // its possible to create target less mappings
        // this is not ideal, however its much easier for the admin to manage.
        if (empty($syncableData)) {
            $attributes = $this->whereFrom();
            $attributes['type'] = $type;
        } else {
            foreach ($syncableData as $index => $target) {
                $attributes[$index] = $this->whereFrom();
                $attributes[$index]['type'] = $type;
                // In the case we EVER allow different types for mapping, we must ensure other fields get nullified.
                if ($target instanceof Model) {
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
        }

        DB::table((new Mapping())->getTable())
            ->insert($attributes);
    }
}