<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\QueueLog
 *
 * @property string $queue
 * @property int $size
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\TFactory|null $use_factory
 * @method static Builder<static>|QueueLog forQueue(string $queueName)
 * @method static Builder<static>|QueueLog newModelQuery()
 * @method static Builder<static>|QueueLog newQuery()
 * @method static Builder<static>|QueueLog query()
 * @method static Builder<static>|QueueLog whereCreatedAt($value)
 * @method static Builder<static>|QueueLog whereQueue($value)
 * @method static Builder<static>|QueueLog whereSize($value)
 * @method static Builder<static>|QueueLog whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class QueueLog extends Model
{
    use HasFactory;

    protected $primaryKey = null;

    protected $fillable = [
        'queue', 'size',
    ];

    public $incrementing = false;

    // Scopes
    #[Scope]
    protected function forQueue(Builder $queue, string $queueName): Builder
    {
        return $queue->where('queue', $queueName);
    }
}
