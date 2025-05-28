<?php

namespace App\Services;

use App\Models\Mapping;
use App\Traits\FluentCaller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MappingService
{
    use FluentCaller;

    protected $from;
    protected $target;
    protected ?string $type = null;

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

    public function type(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function resolveMappingRaw(): Builder
    {
        return Mapping::where($this->whereFrom());
    }

    public function retrieveResolvableRaw(): Builder
    {
        return Mapping::where($this->whereTarget());
    }

    public function resolveMapping(): Collection
    {
        return $this->resolveMappingRaw()->get();
    }

    public function retrieveResolvable(): Collection
    {
        return $this->retrieveResolvableRaw()->get();
    }

    public function mappingExists(): bool
    {
        return$this->resolveMappingRaw()->exists();
    }

    public function mappingDoesntExist(): bool
    {
        return ! $this->mappingExists();
    }

    public function resolveTarget(): Collection
    {
        $data = [];
        foreach ($this->resolveMapping() as $mapping) {
            if ($mapping instanceof Mapping) {
                if (! empty($mapping->target_data)) {
                    $data[] = $mapping->target_data;
                }
                if (! is_null($mapping->target_value)) {
                    $data[] = $mapping->target_value;
                }
                if (! is_null($mapping->target_model_type)) {
                    $data[] = $mapping->mappable;
                }
            }
        }

        return collect($data);
    }

    public function whereFrom(): array
    {
        return $this->whereStruct('from');
    }

    public function whereTarget(): array
    {
        return $this->whereStruct('target');
    }

    public function sync(array $syncableData = [], ?string $type = null): void
    {
        // first we will remove the current rows.
        // TODO: Detach uses whereFrom, which uses $this->type. Perhaps set $this->type for sync also?
        $this->detach();

        // its possible to create target less mappings
        // this is not ideal, however its much easier for the admin to manage.
        if (empty($syncableData)) {
            $attributes = $this->whereFrom();
            $attributes['type'] = $type;
        } else {
            $attributes = [];
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

    public function detach(): void
    {
        Mapping::where($this->whereFrom())->delete();
    }

    private function whereStruct(string $column): array
    {
        if ($this->{$column} instanceof Model) {
            $wheres = [
                "{$column}_model_type" => $this->{$column}->getMorphClass(),
                "{$column}_model_id" => $this->{$column}->id,
            ];
        } elseif (! is_null($this->{$column})) {
            $wheres = ["{$column}_value" => $this->{$column}];
        }

        if (! is_null($this->type)) {
            $wheres['type'] = $this->type;
        }

        // If no valid values were passed, we return an impossible where.
        return $wheres ?? ['id' => -1];
    }
}
