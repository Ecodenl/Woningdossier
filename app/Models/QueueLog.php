<?php

namespace App\Models;

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
 * @method static Builder|QueueLog forQueue(string $queueName)
 * @method static Builder|QueueLog newModelQuery()
 * @method static Builder|QueueLog newQuery()
 * @method static Builder|QueueLog query()
 * @method static Builder|QueueLog whereCreatedAt($value)
 * @method static Builder|QueueLog whereQueue($value)
 * @method static Builder|QueueLog whereSize($value)
 * @method static Builder|QueueLog whereUpdatedAt($value)
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
    public function scopeForQueue(Builder $queue, string $queueName): Builder
    {
        return $queue->where('queue', $queueName);
    }
}
