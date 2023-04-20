<?php

namespace App\Services\Models;

use App\Helpers\Queue;
use App\Models\QueueLog;
use App\Traits\FluentCaller;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class QueueLogService
{
    use FluentCaller;

    protected string $queue = Queue::DEFAULT;
    protected Carbon $from;
    protected Carbon $to;

    public function forQueue(string $queue): self
    {
        $this->queue = $queue;
        return $this;
    }

    public function from(Carbon $from): self
    {
        $this->from = $from;
        return $this;
    }

    public function to(Carbon $to): self
    {
        $this->to = $to;
        return $this;
    }

    public function getAverage(): int
    {
        // Note: Should this be float? Average might be decimal.
        return $this->buildQuery()->average('size') ?? 0;
    }

    public function getMin(): int
    {
        return $this->buildQuery()->min('size') ?? 0;
    }

    public function getMax(): int
    {
        return $this->buildQuery()->max('size') ?? 0;
    }

    protected function buildQuery(): Builder
    {
        return QueueLog::forQueue($this->queue)
            ->when(isset($this->from), function (Builder $query) {
                $query->where('created_at', '>=', $this->from);
            })
            ->when(isset($this->to), function (Builder $query) {
                $query->where('created_at', '<=', $this->to);
            });
    }
}