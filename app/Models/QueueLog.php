<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QueueLog extends Model
{
    use HasFactory;

    protected $primaryKey = null;

    protected $fillable = [
        'queue', 'size',
    ];

    public $incrementing = false;

    // Scopes
    public function scopeForQueue(Builder $queue, string $queueName): Builder
    {
        return $queue->where('queue', $queueName);
    }
}
