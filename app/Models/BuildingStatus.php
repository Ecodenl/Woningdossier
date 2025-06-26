<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingStatus
 *
 * @property int $id
 * @property int $building_id
 * @property int $status_id
 * @property \Illuminate\Support\Carbon|null $appointment_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Status $status
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingStatus mostRecent()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingStatus whereAppointmentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingStatus whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingStatus whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingStatus whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BuildingStatus extends Model
{
    protected $fillable = [
        'status_id', 'building_id', 'appointment_date',
    ];

    protected $with = [
        'status',
    ];

    protected function casts(): array
    {
        return [
            'appointment_date' => 'datetime',
        ];
    }

    #[Scope]
    protected function mostRecent($query)
    {
        // the higher the id the newer it is, ideally we would want to order on the created_at
        // but we cant rely on that because of migration from another table.
        return $query->orderByDesc('id');
    }

    public function hasAppointmentDate(): bool
    {
        return $this->appointment_date instanceof \DateTime;
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }
}
