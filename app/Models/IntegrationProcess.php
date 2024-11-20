<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\IntegrationProcess
 *
 * @property int $id
 * @property int $integration_id
 * @property int $building_id
 * @property string $process
 * @property \Illuminate\Support\Carbon|null $synced_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IntegrationProcess newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IntegrationProcess newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IntegrationProcess query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IntegrationProcess whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IntegrationProcess whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IntegrationProcess whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IntegrationProcess whereIntegrationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IntegrationProcess whereProcess($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IntegrationProcess whereSyncedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IntegrationProcess whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class IntegrationProcess extends Model
{
    use HasFactory;

    protected $fillable = [
        'integration_id',
        'building_id',
        'process',
        'synced_at',
    ];

    protected function casts(): array
    {
        return [
            'synced_at' => 'datetime:Y-m-d H:i:s'
        ];
    }
}
