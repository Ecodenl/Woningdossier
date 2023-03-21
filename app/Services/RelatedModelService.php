<?php

namespace App\Services;

use App\Models\RelatedModel;
use App\Traits\FluentCaller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class RelatedModelService
{
    use FluentCaller;

    protected $from;
    protected $target;

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

    public function resolveTargetRaw(): Builder
    {
        return RelatedModel::where($this->whereFrom());
    }

    public function resolveTarget(): Collection
    {
        return $this->resolveTargetRaw()->get();
    }

    public function retrieveFromRaw(): Builder
    {
        return RelatedModel::where($this->whereTarget());
    }

    public function retrieveFrom(): Collection
    {
        return $this->retrieveFromRaw()->get();
    }

    public function whereFrom(): array
    {
        return $this->whereStruct('from');
    }

    public function whereTarget(): array
    {
        return $this->whereStruct('target');
    }

    private function whereStruct(string $column): array
    {
        if ($this->{$column} instanceof Model) {
            $wheres = [
                "{$column}_model_type" => $this->{$column}->getMorphClass(),
                "{$column}_model_id" => $this->{$column}->id,
            ];
        }

        // If no valid values were passed, we return an impossible where.
        return $wheres ?? ['id' => -1];
    }
}
